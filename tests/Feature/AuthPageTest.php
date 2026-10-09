<?php

use App\Mail\RegistrationVerificationMail;
use App\Models\RegistrationToken;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

test('the home page is available', function () {
    $response = $this->get('/');

    $response
        ->assertOk()
        ->assertViewIs('home')
        ->assertSee('เนื้อหาอัปเดตล่าสุด')
        ->assertSee('วิชายอดนิยม')
        ->assertSee('data-course-carousel', false)
        ->assertSee('data-carousel-next', false)
        ->assertSee('Footer');
});

test('the home page exposes a loading skeleton state', function () {
    $this->get('/?loading=1')
        ->assertOk()
        ->assertSee('aria-busy="true"', false)
        ->assertSee('กำลังโหลดหน้าแรก');
});

test('a course detail page is available by course code', function () {
    $this->get(route('courses.show', 'CP422301'))
        ->assertOk()
        ->assertViewIs('course.show')
        ->assertSee('CP422301')
        ->assertSee('ความคิดเห็น');
});

test('an unknown course code returns not found', function () {
    $this->get(route('courses.show', 'CP000000'))->assertNotFound();
});

test('login and registration pages are available', function () {
    $this->get(route('auth.login'))
        ->assertOk()
        ->assertViewIs('auth.login');

    $this->get(route('auth.register'))
        ->assertOk()
        ->assertViewIs('auth.register');
});

test('a registration request sends a verification email without authenticating the user', function () {
    Mail::fake();

    $response = $this->post(route('auth.register.store'), [
        'email' => 'ada@example.com',
    ]);

    $response->assertRedirect(route('auth.verify-email', ['email' => 'ada@example.com']));
    $this->assertGuest();
    $this->assertDatabaseHas('registration_tokens', ['email' => 'ada@example.com']);
    Mail::assertSent(RegistrationVerificationMail::class, function (RegistrationVerificationMail $mail) {
        $html = $mail->render();

        return str_contains($html, 'ยืนยันอีเมลของคุณ')
            && str_contains($html, 'ลิงก์นี้จะหมดอายุภายใน 15 นาที')
            && str_contains($html, 'ยืนยันอีเมล');
    });
});

test('a user completes registration after verifying the email', function () {
    $registration = RegistrationToken::create([
        'email' => 'ada@example.com',
        'token_hash' => Hash::make('test-token'),
        'expires_at' => now()->addMinutes(15),
    ]);
    $verificationUrl = URL::temporarySignedRoute(
        'auth.registration.verify',
        now()->addMinutes(15),
        ['registration' => $registration->id, 'token' => 'test-token'],
    );

    $this->get($verificationUrl)->assertRedirectToRoute('auth.set-password');
    $this->post(route('auth.set-password.store'), [
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirectToRoute('auth.set-username');
    $this->post(route('auth.set-username.store'), ['name' => 'Ada Lovelace'])
        ->assertRedirectToRoute('home');

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', ['email' => 'ada@example.com', 'name' => 'Ada Lovelace']);
});

test('a registration verification token cannot be reused', function () {
    $registration = RegistrationToken::create([
        'email' => 'ada@example.com',
        'token_hash' => Hash::make('test-token'),
        'expires_at' => now()->subMinute(),
    ]);
    $url = URL::temporarySignedRoute(
        'auth.registration.verify',
        now()->addMinutes(15),
        ['registration' => $registration->id, 'token' => 'test-token'],
    );

    $this->get($url)->assertStatus(410);
});

test('a Google-only account can complete email registration with the same email', function () {
    $user = User::factory()->create([
        'email' => 'ada@example.com',
        'password' => null,
    ]);
    $registration = RegistrationToken::create([
        'email' => $user->email,
        'token_hash' => Hash::make('test-token'),
        'expires_at' => now()->addMinutes(15),
    ]);
    $url = URL::temporarySignedRoute(
        'auth.registration.verify',
        now()->addMinutes(15),
        ['registration' => $registration->id, 'token' => 'test-token'],
    );

    $this->get($url);
    $this->post(route('auth.set-password.store'), [
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);
    $this->post(route('auth.set-username.store'), ['name' => 'Ada Lovelace']);

    expect($user->fresh()->password)->not->toBeNull();
    $this->assertAuthenticatedAs($user->fresh());
});

test('a user can log in and log out', function () {
    $user = User::factory()->create(['password' => 'password123']);

    $this->post(route('auth.login.store'), [
        'email' => $user->email,
        'password' => 'password123',
    ])->assertRedirectToRoute('home');

    $this->assertAuthenticatedAs($user);

    $this->post(route('auth.logout'))->assertRedirectToRoute('auth.login');
    $this->assertGuest();
});

test('invalid login credentials are rejected', function () {
    $user = User::factory()->create(['password' => 'password123']);

    $this->from(route('auth.login'))
        ->post(route('auth.login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
        ->assertRedirect(route('auth.login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('a user can request a password reset link', function () {
    Notification::fake();
    $user = User::factory()->create(['email' => 'ada@example.com']);

    $response = $this->post(route('auth.forgot-password.store'), [
        'email' => $user->email,
    ]);

    $response
        ->assertRedirectToRoute('auth.password-reset-sent')
        ->assertSessionHas('password_reset_email', $user->email);
    Notification::assertSentTo($user, ResetPassword::class);
});

test('a user can reset a password with a valid reset token', function () {
    $user = User::factory()->create(['password' => 'old-password']);
    $token = Password::broker()->createToken($user);

    $this->get(route('password.reset', [
        'token' => $token,
        'email' => $user->email,
    ]))->assertOk()->assertViewIs('auth.reset-password');

    $this->post(route('auth.reset-password.store'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertRedirectToRoute('auth.login');

    $this->assertGuest();
    $this->assertTrue(password_verify('new-password', $user->fresh()->password));
});

test('guests cannot access authenticated setup pages', function () {
    $this->get(route('auth.set-password'))
        ->assertRedirectToRoute('auth.register');

    $this->get(route('auth.set-username'))
        ->assertRedirectToRoute('auth.set-password');
});

test('authentication flow pages are available', function () {
    collect([
        'auth.verify-email',
        'auth.forgot-password',
        'auth.password-reset-sent',
    ])->each(function (string $routeName) {
        $this->get(route($routeName))->assertOk();
    });

    $this->get(route('auth.set-password'))->assertRedirectToRoute('auth.register');
});
