<?php

namespace App\Http\Controllers;

use App\Actions\Auth\RegistrationService;
use App\Models\RegistrationToken;
use App\Models\SocialAccount;
use App\Models\User;
use App\Rules\KkuMailEmail;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

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
        $this->normalizeEmailInput($request);

        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
                new KkuMailEmail,
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
        try {
            return Socialite::driver('google')->redirect();
        } catch (Throwable $exception) {
            report($exception);

            return $this->googleFailure('ยังเชื่อมต่อ Google ไม่ได้ในขณะนี้ กรุณาลองอีกครั้ง');
        }
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        if ($request->filled('error')) {
            return $this->googleFailure('การดำเนินการด้วย Google ถูกยกเลิก คุณสามารถลองอีกครั้งได้');
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $exception) {
            report($exception);

            return $this->googleFailure('เชื่อมต่อ Google ไม่สำเร็จหรือการยืนยันหมดอายุ กรุณาลองอีกครั้ง');
        }

        $email = $googleUser->getEmail();

        if (! KkuMailEmail::accepts($email)) {
            return $this->googleFailure('กรุณาใช้บัญชี Google ของมหาวิทยาลัยที่ลงท้ายด้วย @kkumail.com');
        }

        if (($googleUser->getRaw()['email_verified'] ?? false) !== true) {
            return $this->googleFailure('บัญชี Google นี้ยังไม่ได้ยืนยันอีเมล');
        }

        if (! $googleUser->getId()) {
            return $this->googleFailure('Google ไม่ได้ส่งข้อมูลบัญชีที่จำเป็น กรุณาลองอีกครั้ง');
        }

        try {
            $user = DB::transaction(function () use ($googleUser, $email): ?User {
                $socialAccount = SocialAccount::query()
                    ->where('provider', 'google')
                    ->where('provider_user_id', $googleUser->getId())
                    ->first();

                if ($socialAccount && $socialAccount->user?->email !== Str::lower($email)) {
                    return null;
                }

                $user = $socialAccount?->user ?? User::firstOrCreate(
                    ['email' => Str::lower($email)],
                    [
                        'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'ผู้ใช้ Google',
                        'password' => null,
                        'email_verified_at' => now(),
                        'role' => 'user',
                    ],
                );

                if ($user->googleAccount && $user->googleAccount->provider_user_id !== $googleUser->getId()) {
                    return null;
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

                return $user;
            });
        } catch (QueryException $exception) {
            report($exception);

            return $this->googleFailure('บันทึกการเชื่อมต่อ Google ไม่สำเร็จ กรุณาลองเข้าสู่ระบบอีกครั้ง');
        }

        if (! $user) {
            return $this->googleFailure('บัญชี Google นี้เชื่อมกับบัญชีอื่นอยู่ กรุณาใช้บัญชีเดิมหรือเข้าสู่ระบบด้วยอีเมล');
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return $this->redirectAfterAuthentication($request, $user);
    }

    public function register(Request $request): RedirectResponse
    {
        $this->normalizeEmailInput($request);

        $validated = $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                new KkuMailEmail,
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

        try {
            $registrationToken = $this->registrationService->sendVerification($pendingRegistration->email);
        } catch (ValidationException $exception) {
            return redirect()->route('auth.verify-email')
                ->with('verification_resend_error', $exception->errors()['email'][0]);
        }
        $request->session()->put('pending_registration_id', $registrationToken->id);

        return redirect()
            ->route('auth.verify-email')
            ->with('verification_resent', true);
    }

    public function verifyRegistration(Request $request, RegistrationToken $registration, string $token): RedirectResponse
    {
        if (! KkuMailEmail::accepts($registration->email)) {
            return redirect()->route('auth.register')->with('verification_link_error', 'กรุณาใช้อีเมล @kkumail.com สมัครสมาชิก');
        }

        if ($registration->used_at) {
            return redirect()
                ->route('auth.login')
                ->with('status', 'อีเมลนี้ยืนยันแล้ว กรุณาเข้าสู่ระบบ');
        }

        if ($registration->expires_at->isPast()) {
            $request->session()->put([
                'pending_registration_id' => $registration->id,
                'registration_email' => $registration->email,
            ]);

            return redirect()
                ->route('auth.verify-email')
                ->with('verification_link_error', 'ลิงก์ยืนยันอีเมลหมดอายุแล้ว กรุณาส่งลิงก์ใหม่');
        }

        if (! Hash::check($token, $registration->token_hash)) {
            return redirect()
                ->route('auth.register')
                ->with('verification_link_error', 'ลิงก์ยืนยันอีเมลไม่ถูกต้อง กรุณาสมัครสมาชิกใหม่อีกครั้ง');
        }

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

        try {
            $user = DB::transaction(function () use ($registration, $request, $validated): ?User {
                $activeRegistration = RegistrationToken::query()->lockForUpdate()->find($registration->id);

                if (! $activeRegistration || $activeRegistration->used_at || $activeRegistration->expires_at->isPast()) {
                    return null;
                }

                $user = User::query()->where('email', $activeRegistration->email)->lockForUpdate()->first()
                    ?? new User(['email' => $activeRegistration->email]);

                if ($user->password) {
                    return null;
                }

                $user->forceFill([
                    'name' => $validated['name'],
                    'password' => $request->session()->get('registration_password'),
                    'email_verified_at' => now(),
                ])->save();

                $activeRegistration->update(['used_at' => now()]);

                return $user;
            });
        } catch (QueryException $exception) {
            report($exception);

            return back()
                ->withErrors(['name' => 'บันทึกบัญชีไม่สำเร็จ กรุณาลองอีกครั้ง'])
                ->onlyInput('name');
        }

        if (! $user) {
            $this->registrationService->forgetSession($request);

            return redirect()->route('auth.login')
                ->with('status', 'ลิงก์นี้ถูกใช้แล้วหรือบัญชีถูกสร้างแล้ว กรุณาเข้าสู่ระบบ');
        }

        $this->registrationService->forgetSession($request);
        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectAfterAuthentication($request, $user);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
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
        $this->normalizeEmailInput($request);

        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                new KkuMailEmail,
            ],
        ]);

        try {
            $status = Password::sendResetLink(['email' => $validated['email']]);
        } catch (Throwable $exception) {
            report($exception);

            $user = User::query()->where('email', $validated['email'])->first();

            if ($user) {
                Password::broker()->deleteToken($user);
            }

            return back()
                ->withInput($request->only('email'))
                ->with('reset_link_error', 'ส่งลิงก์รีเซ็ตรหัสผ่านไม่สำเร็จ กรุณาลองอีกครั้งในภายหลัง');
        }

        if ($status === Password::RESET_THROTTLED) {
            return back()
                ->withInput($request->only('email'))
                ->with('reset_link_error', 'ส่งคำขอบ่อยเกินไป กรุณารอสักครู่แล้วลองใหม่');
        }

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
        $user = KkuMailEmail::accepts($email) ? User::query()->where('email', Str::lower($email))->first() : null;

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
        $this->normalizeEmailInput($request);

        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', new KkuMailEmail],
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
            return redirect()
                ->route('auth.forgot-password')
                ->with('reset_link_error', 'ลิงก์รีเซ็ตรหัสผ่านไม่ถูกต้องหรือหมดอายุ กรุณาขอลิงก์ใหม่');
        }

        $request->session()->forget('password_reset_email');

        return redirect()->route('auth.login')->with('status', 'รีเซ็ตรหัสผ่านสำเร็จแล้ว');
    }

    private function redirectAfterAuthentication(Request $request, User $user): RedirectResponse
    {
        if (! $user->isAdmin() && ! $user->hasCompletedProfile()) {
            return redirect()->route('profile.complete');
        }

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

    private function normalizeEmailInput(Request $request): void
    {
        $email = $request->input('email');

        if (is_string($email)) {
            $request->merge(['email' => Str::lower(trim($email))]);
        }
    }

    private function googleFailure(string $message): RedirectResponse
    {
        return redirect()->route('auth.login')->with('google_error', $message);
    }
}
