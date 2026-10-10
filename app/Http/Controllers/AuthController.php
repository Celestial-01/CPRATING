<?php

namespace App\Http\Controllers;

use App\Mail\RegistrationVerificationMail;
use App\Models\RegistrationToken;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
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

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();
        $socialAccount = SocialAccount::where('provider', 'google')
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

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return redirect()->intended(route('home'));
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

        $existingUser = User::where('email', $validated['email'])->first();

        if ($existingUser?->password) {
            throw ValidationException::withMessages([
                'email' => 'อีเมลนี้มีบัญชีอยู่แล้ว กรุณาเข้าสู่ระบบแทน',
            ]);
        }

        $registrationToken = $this->sendRegistrationVerification($validated['email']);

        return redirect()
            ->route('auth.verify-email', ['email' => $registrationToken->email])
            ->with('registration_email', $registrationToken->email);
    }

    public function resendRegistrationVerification(Request $request): RedirectResponse
    {
        $email = $request->session()->get('registration_email', $request->input('email'));

        $validated = validator(
            ['email' => $email],
            ['email' => ['required', 'email']],
        )->validate();

        if (User::where('email', $validated['email'])->whereNotNull('password')->exists()) {
            return redirect()->route('auth.login');
        }

        $registrationToken = $this->sendRegistrationVerification($validated['email']);

        return redirect()
            ->route('auth.verify-email', ['email' => $registrationToken->email])
            ->with('registration_email', $registrationToken->email)
            ->with('verification_resent', true);
    }

    private function sendRegistrationVerification(string $email): RegistrationToken
    {
        $plainToken = Str::random(64);
        $registrationToken = RegistrationToken::updateOrCreate(
            ['email' => $email],
            [
                'token_hash' => Hash::make($plainToken),
                'expires_at' => now()->addMinutes(15),
                'used_at' => null,
            ],
        );

        $verificationUrl = URL::temporarySignedRoute(
            'auth.registration.verify',
            now()->addMinutes(15),
            [
                'registration' => $registrationToken->id,
                'token' => $plainToken,
            ],
        );

        Mail::to($registrationToken->email)->send(new RegistrationVerificationMail($verificationUrl));

        return $registrationToken;
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

        $request->session()->put('registration_token_id', $registration->id);
        $request->session()->put('registration_email', $registration->email);

        return redirect()->route('auth.set-password');
    }

    public function setPassword(Request $request): RedirectResponse
    {
        $registration = $this->registrationToken($request);

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
        $registration = $this->registrationToken($request);

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
        $request->session()->forget([
            'registration_token_id',
            'registration_email',
            'registration_password',
        ]);
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

    public function showVerifyEmail(Request $request): View
    {
        return view('auth.verify-email', [
            'email' => $request->session()->get('registration_email', $request->query('email', '')),
        ]);
    }

    public function showSetPassword(Request $request): View|RedirectResponse
    {
        return $this->registrationToken($request)
            ? view('auth.set-password')
            : redirect()->route('auth.register');
    }

    public function showSetUsername(Request $request): View|RedirectResponse
    {
        $registration = $this->registrationToken($request);

        if (! $registration || ! $request->session()->has('registration_password')) {
            return redirect()->route('auth.set-password');
        }

        $suggestedUsername = Str::lower(Str::random(8));

        return view('auth.set-username', compact('suggestedUsername'));
    }

    private function registrationToken(Request $request): ?RegistrationToken
    {
        $id = $request->session()->get('registration_token_id');

        if (! $id) {
            return null;
        }

        $registration = RegistrationToken::find($id);

        if (! $registration || $registration->used_at || $registration->expires_at->isPast()) {
            $request->session()->forget([
                'registration_token_id',
                'registration_email',
                'registration_password',
            ]);

            return null;
        }

        return $registration;
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

        return redirect()
            ->route('auth.password-reset-sent')
            ->with('password_reset_email', $validated['email']);
    }

    public function showPasswordResetSent(Request $request): View
    {
        return view('auth.password-reset-sent', [
            'email' => $request->session()->get('password_reset_email', $request->query('email', '')),
        ]);
    }

    public function showResetPassword(string $token, Request $request): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
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

        return redirect()->route('auth.login')->with('status', 'รีเซ็ตรหัสผ่านสำเร็จแล้ว');
    }
}
