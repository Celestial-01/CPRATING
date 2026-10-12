<?php

namespace App\Http\Controllers;

use App\Actions\Auth\RegistrationService;
use App\Models\RegistrationToken;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function __construct(private readonly RegistrationService $registrationService) {}

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
                'string',
            ],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง'])
                ->onlyInput('email');
        }

        /** @var User $user */
        $user = Auth::user();
        $request->session()->regenerate();

        return $this->redirectAfterAuthentication($request, $user);
    }

    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();
        $socialAccount = SocialAccount::query()
            ->where('provider', 'google')
            ->where('provider_user_id', $googleUser->getId())
            ->first();

        $user = $socialAccount?->user;

        if (! $user) {
            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'ผู้ใช้ Google',
                    'password' => null,
                    'email_verified_at' => now(),
                    'role' => 'user',
                ],
            );

            SocialAccount::firstOrCreate([
                'provider' => 'google',
                'provider_user_id' => $googleUser->getId(),
            ], [
                'user_id' => $user->id,
            ]);
        }

        SocialAccount::updateOrCreate(
            [
                'provider' => 'google',
                'provider_user_id' => $googleUser->getId(),
            ],
            [
                'user_id' => $user->id,
                'avatar_url' => $googleUser->getAvatar(),
            ],
        );

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return $this->redirectAfterAuthentication($request, $user);
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
            ],
        ]);

        $existingUser = User::query()->where('email', $validated['email'])->first();

        if ($existingUser?->password) {
            throw ValidationException::withMessages([
                'email' => 'อีเมลนี้มีบัญชีอยู่แล้ว กรุณาเข้าสู่ระบบแทน',
            ]);
        }

        $registrationToken = $this->registrationService->sendVerification($validated['email']);

        $request->session()->put('pending_registration_id', $registrationToken->id);
        $request->session()->put('registration_email', $registrationToken->email);

        return redirect()
            ->route('auth.verify-email');
    }

    public function resendRegistrationVerification(Request $request): RedirectResponse
    {
        $pendingRegistration = $this->registrationService->pendingToken($request);

        if (! $pendingRegistration) {
            return redirect()->route('auth.register');
        }

        if (User::query()->where('email', $pendingRegistration->email)->whereNotNull('password')->exists()) {
            return redirect()->route('auth.login');
        }

        $registrationToken = $this->registrationService->sendVerification($pendingRegistration->email);
        $request->session()->put('pending_registration_id', $registrationToken->id);

        return redirect()
            ->route('auth.verify-email')
            ->with('verification_resent', true);
    }

    public function verifyRegistration(Request $request, RegistrationToken $registration, string $token): RedirectResponse
    {
        abort_if(
            $registration->used_at ||
            $registration->expires_at->isPast() ||
            ! Hash::check($token, $registration->token_hash),
            410,
            'ลิงก์สมัครสมาชิกหมดอายุหรือถูกใช้งานแล้ว',
        );

        $request->session()->forget('pending_registration_id');
        $request->session()->put('registration_token_id', $registration->id);
        $request->session()->put('registration_email', $registration->email);

        return redirect()->route('auth.set-password');
    }

    public function setPassword(Request $request): RedirectResponse
    {
        $registration = $this->registrationService->activeToken($request);

        if (! $registration) {
            return redirect()->route('auth.register');
        }

        $validated = $request->validate([
            'password' => [
                'required',
                'confirmed',
                'string',
                'min:8',
            ],
        ]);

        $request->session()->put('registration_password', Hash::make($validated['password']));

        return redirect()->route('auth.set-username');
    }

    public function setUsername(Request $request): RedirectResponse
    {
        $registration = $this->registrationService->activeToken($request);

        if (! $registration || ! $request->session()->has('registration_password')) {
            return redirect()->route('auth.set-password');
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        $user = User::firstOrNew(['email' => $registration->email]);
        $user->forceFill([
            'name' => $validated['name'],
            'password' => $request->session()->get('registration_password'),
            'email_verified_at' => now(),
        ])->save();

        $registration->update(['used_at' => now()]);
        $this->registrationService->forgetSession($request);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login');
    }

    public function showVerifyEmail(Request $request): View|RedirectResponse
    {
        $registration = $this->registrationService->pendingToken($request);

        if (! $registration) {
            return redirect()->route('auth.register');
        }

        return view('auth.verify-email', [
            'email' => $registration->email,
        ]);
    }

    public function showSetPassword(Request $request): View|RedirectResponse
    {
        return $this->registrationService->activeToken($request)
            ? view('auth.set-password')
            : redirect()->route('auth.register');
    }

    public function showSetUsername(Request $request): View|RedirectResponse
    {
        $registration = $this->registrationService->activeToken($request);

        if (! $registration || ! $request->session()->has('registration_password')) {
            return redirect()->route('auth.set-password');
        }

        $suggestedUsername = Str::lower(Str::random(8));

        return view('auth.set-username', ['suggestedUsername' => $suggestedUsername]);
    }

    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function sendPasswordResetLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
            ],
        ]);

        Password::sendResetLink(['email' => $validated['email']]);

        $request->session()->put('password_reset_email', $validated['email']);

        return redirect()
            ->route('auth.password-reset-sent');
    }

    public function showPasswordResetSent(Request $request): View|RedirectResponse
    {
        $email = $request->session()->get('password_reset_email');

        if (! $email) {
            return redirect()->route('auth.forgot-password');
        }

        return view('auth.password-reset-sent', [
            'email' => $email,
        ]);
    }

    public function showResetPassword(string $token, Request $request): View|RedirectResponse
    {
        $email = $request->query('email');
        $user = is_string($email) ? User::query()->where('email', $email)->first() : null;

        if (! $user || ! Password::broker()->tokenExists($user, $token)) {
            return redirect()
                ->route('auth.forgot-password')
                ->with('reset_link_error', 'ลิงก์รีเซ็ตรหัสผ่านไม่ถูกต้องหรือหมดอายุ กรุณาขอลิงก์ใหม่');
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'confirmed',
                'string',
                'min:8',
            ],
        ]);

        $status = Password::reset(
            $validated,
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
        }

        $request->session()->forget('password_reset_email');

        return redirect()->route('auth.login')->with('status', 'รีเซ็ตรหัสผ่านสำเร็จแล้ว');
    }

    private function redirectAfterAuthentication(Request $request, User $user): RedirectResponse
    {
        if (! $user->isAdmin() && $this->isAdminIntendedUrl($request->session()->get('url.intended'))) {
            $request->session()->forget('url.intended');
        }

        return redirect()->intended(route('home'));
    }

    private function isAdminIntendedUrl(mixed $url): bool
    {
        if (! is_string($url)) {
            return false;
        }

        $path = parse_url($url, PHP_URL_PATH);

        return is_string($path)
            && ($path === '/admin' || str_starts_with($path, '/admin/'));
    }
}
