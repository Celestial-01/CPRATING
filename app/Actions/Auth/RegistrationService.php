<?php

namespace App\Actions\Auth;

use App\Mail\RegistrationVerificationMail;
use App\Models\RegistrationToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Mime\Exception\RfcComplianceException;

final class RegistrationService
{
    private const TOKEN_TTL_MINUTES = 15;

    public function sendVerification(string $email): RegistrationToken
    {
        $email = $this->normalizeEmail($email);

        $plainToken = Str::random(64);
        $registrationToken = RegistrationToken::updateOrCreate(
            ['email' => $email],
            [
                'token_hash' => Hash::make($plainToken),
                'expires_at' => now()->addMinutes(self::TOKEN_TTL_MINUTES),
                'used_at' => null,
            ],
        );

        $verificationUrl = URL::temporarySignedRoute(
            'auth.registration.verify',
            now()->addMinutes(self::TOKEN_TTL_MINUTES),
            [
                'registration' => $registrationToken->id,
                'token' => $plainToken,
            ],
        );

        try {
            Mail::to($registrationToken->email)->send(new RegistrationVerificationMail($verificationUrl));
        } catch (RfcComplianceException) {
            $registrationToken->delete();

            throw ValidationException::withMessages([
                'email' => 'ไม่สามารถส่งอีเมลยืนยันไปยังอีเมลนี้ได้ กรุณาตรวจสอบอีเมลแล้วลองใหม่อีกครั้ง',
            ]);
        }

        return $registrationToken;
    }

    private function normalizeEmail(string $email): string
    {
        return Str::lower(trim($email));
    }

    public function activeToken(Request $request): ?RegistrationToken
    {
        $id = $request->session()->get('registration_token_id');

        if (! $id) {
            return null;
        }

        $registration = RegistrationToken::query()->find($id);

        if ($registration && ! $registration->used_at && ! $registration->expires_at->isPast()) {
            return $registration;
        }

        $this->forgetSession($request);

        return null;
    }

    public function pendingToken(Request $request): ?RegistrationToken
    {
        $id = $request->session()->get('pending_registration_id');
        $email = $request->session()->get('registration_email');

        if (! $id || ! $email) {
            return null;
        }

        $registration = RegistrationToken::query()->find($id);

        if ($registration
            && $registration->email === $email
            && ! $registration->used_at) {
            return $registration;
        }

        $request->session()->forget(['pending_registration_id', 'registration_email']);

        return null;
    }

    public function forgetSession(Request $request): void
    {
        $request->session()->forget([
            'pending_registration_id',
            'registration_token_id',
            'registration_email',
            'registration_password',
        ]);
    }
}
