<?php

use App\Mail\RegistrationVerificationMail;
use App\Models\RegistrationToken;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Symfony\Component\Mime\Exception\RfcComplianceException;

uses(RefreshDatabase::class);

test('the home page is available', function () {
    $response = $this->get('/');

    $response
        ->assertOk()
        ->assertViewIs('home')
        ->assertSee('วิชายอดนิยม')
        ->assertSee('ไฮไลต์')
        ->assertSee('เสียงจาก')
        ->assertSee('คำถามที่พบบ่อย')
        ->assertSee('CPRATING');
});

test('catalog and study plan pages are available', function () {
    $this->get(route('courses.index'))
        ->assertOk()
        ->assertViewIs('courses.index')
        ->assertSee('ค้นหารายวิชา');

    $this->get(route('courses.index', ['q' => 'CP422301']))
        ->assertOk()
        ->assertSee('CP422301');

    $this->get(route('plans.index'))
        ->assertOk()
        ->assertViewIs('plans.index')
        ->assertSee('เลือก')
        ->assertSee('หลักสูตร');
});

test('history page is available with viewed and saved sections', function () {
    $this->get(route('history.index'))
        ->assertOk()
        ->assertViewIs('history.index')
        ->assertSee('วิชาที่คุณเพิ่งเปิดดู')
        ->assertSee('วิชาที่คุณเก็บไว้')
        ->assertSee('data-history-show="viewed"', false)
        ->assertSee('data-history-show="saved"', false)
        ->assertSee('data-history-rail-prev="viewed"', false)
        ->assertSee('data-history-rail-next="viewed"', false)
        ->assertSee('data-history-rail-prev="saved"', false)
        ->assertSee('data-history-rail-next="saved"', false)
        ->assertSee('กลับสู่ภาพรวม')
        ->assertDontSee('ล้างประวัติ')
        ->assertSee('ประวัติ');
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
        ->assertViewIs('courses.show')
        ->assertSee('CP422301')
        ->assertSee('data-related-courses', false)
        ->assertSee('class="cc"', false)
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

test('an authenticated navbar displays the user name, email, and Google avatar', function () {
    $user = User::factory()->create([
        'name' => 'Patiphan',
        'email' => 'patiphan@example.com',
    ]);

    SocialAccount::create([
        'user_id' => $user->id,
        'provider' => 'google',
        'provider_user_id' => 'google-user-id',
        'avatar_url' => 'https://example.com/avatar.jpg',
    ]);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('data-user-menu', false)
        ->assertSee('Patiphan')
        ->assertSee('patiphan@example.com')
        ->assertSee('https://example.com/avatar.jpg');
});

test('an admin can open the dashboard from the authenticated navbar', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('home'))
        ->assertOk()
        ->assertSee(route('admin.dashboard'), false);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertViewIs('admin.dashboard');
});

test('a regular user cannot see or access the admin dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertDontSee(route('admin.dashboard'), false);

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

test('the admin courses path opens the existing admin UI', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/admin/courses/')
        ->assertRedirectToRoute('admin.dashboard');

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertViewIs('admin.dashboard');
});

test('a regular user cannot open the admin courses path', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/admin/courses/')
        ->assertForbidden();
});

test('admin pages from the partner baseline render with the current route structure', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertViewIs('admin.dashboard');

    $this->actingAs($admin)
        ->get(route('admin.courses.create'))
        ->assertOk()
        ->assertViewIs('admin.courses.create');

    $this->actingAs($admin)
        ->get(route('admin.teachers.index'))
        ->assertOk()
        ->assertViewIs('admin.teachers.index');

    $this->actingAs($admin)
        ->get(route('admin.topics.index'))
        ->assertOk()
        ->assertViewIs('admin.topics.index');
});

test('a registration request sends a verification email without authenticating the user', function () {
    Mail::fake();

    $response = $this->post(route('auth.register.store'), [
        'email' => 'ada@example.com',
    ]);

    $response->assertRedirectToRoute('auth.verify-email');
    $response->assertSessionHas('registration_email', 'ada@example.com');
    $this->assertGuest();
    $this->assertDatabaseHas('registration_tokens', ['email' => 'ada@example.com']);
    Mail::assertSent(RegistrationVerificationMail::class, function (RegistrationVerificationMail $mail) {
        $html = $mail->render();

        return $mail->hasTo('ada@example.com')
            && str_contains($html, 'ยืนยันอีเมลของคุณ')
            && str_contains($html, 'ลิงก์นี้จะหมดอายุภายใน 15 นาที')
            && str_contains($html, 'ยืนยันอีเมล');
    });
});

test('a registration whose stored email cannot be sent to fails with a Thai validation error instead of a crash', function () {

    Mail::shouldReceive('to')->once()->andThrow(
        new RfcComplianceException('Email "c" does not comply with addr-spec of RFC 2822.'),
    );

    $this->from(route('auth.register'))
        ->post(route('auth.register.store'), ['email' => 'ada@example.com'])
        ->assertRedirect(route('auth.register'))
        ->assertSessionHasErrors(['email' => 'ไม่สามารถส่งอีเมลยืนยันไปยังอีเมลนี้ได้ กรุณาตรวจสอบอีเมลแล้วลองใหม่อีกครั้ง']);

    expect(RegistrationToken::query()->where('email', 'ada@example.com')->exists())->toBeFalse();
});

test('a user can resend the registration verification email', function () {
    Mail::fake();

    $this->post(route('auth.register.store'), ['email' => 'ada@example.com']);

    $this->post(route('auth.verify-email.resend'), ['email' => 'other@example.com'])
        ->assertRedirectToRoute('auth.verify-email')
        ->assertSessionHas('verification_resent', true);

    Mail::assertSent(RegistrationVerificationMail::class, 2);
    Mail::assertNotSent(RegistrationVerificationMail::class, fn (RegistrationVerificationMail $mail) => $mail->hasTo('other@example.com'));
});

test('registration verification resend is rate limited with a friendly response', function () {
    Mail::fake();
    $this->post(route('auth.register.store'), ['email' => 'throttle@example.com']);

    collect(range(1, 3))->each(function (): void {
        $this->post(route('auth.verify-email.resend'));
    });

    $this->post(route('auth.verify-email.resend'))
        ->assertRedirectToRoute('auth.verify-email')
        ->assertSessionHas('verification_resend_error');
});

test('registration verification page requires a pending registration in the session', function () {
    Mail::fake();

    $this->get(route('auth.verify-email'))->assertRedirectToRoute('auth.register');
    $this->get(route('auth.verify-email', ['email' => 'ada@example.com']))
        ->assertRedirectToRoute('auth.register');
    $this->post(route('auth.verify-email.resend'), ['email' => 'ada@example.com'])
        ->assertRedirectToRoute('auth.register');
    Mail::assertNothingSent();

    $this->post(route('auth.register.store'), ['email' => 'ada@example.com']);

    $this->get(route('auth.verify-email', ['email' => 'other@example.com']))
        ->assertOk()
        ->assertSee('ada@example.com')
        ->assertDontSee('other@example.com');
    $this->get(route('auth.verify-email'))->assertOk();
});

test('an expired registration link can be resent from the pending page', function () {
    Mail::fake();
    $this->post(route('auth.register.store'), ['email' => 'ada@example.com']);
    RegistrationToken::query()->where('email', 'ada@example.com')->update([
        'expires_at' => now()->subMinute(),
    ]);

    $this->get(route('auth.verify-email'))->assertOk();
    $this->post(route('auth.verify-email.resend'))
        ->assertRedirectToRoute('auth.verify-email');

    expect(RegistrationToken::query()->where('email', 'ada@example.com')->first()->expires_at->isFuture())
        ->toBeTrue();
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

    $this->post(route('auth.logout'));
    $this->get(route('auth.verify-email'))->assertRedirectToRoute('auth.register');
});

test('an expired registration link returns to the verification page with a resend error', function () {
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

    $this->get($url)
        ->assertRedirectToRoute('auth.verify-email')
        ->assertSessionHas('verification_link_error');

    $this->get(route('auth.verify-email'))
        ->assertOk()
        ->assertSee('ลิงก์ยืนยันอีเมลหมดอายุแล้ว กรุณาส่งลิงก์ใหม่');
});

test('a used registration link sends the user to login with a status message', function () {
    $registration = RegistrationToken::create([
        'email' => 'ada@example.com',
        'token_hash' => Hash::make('test-token'),
        'expires_at' => now()->addMinutes(15),
        'used_at' => now(),
    ]);
    $url = URL::temporarySignedRoute(
        'auth.registration.verify',
        now()->addMinutes(15),
        ['registration' => $registration->id, 'token' => 'test-token'],
    );

    $this->get($url)
        ->assertRedirectToRoute('auth.login')
        ->assertSessionHas('status');

    $this->get(route('auth.login'))
        ->assertOk()
        ->assertSee('อีเมลนี้ยืนยันแล้ว กรุณาเข้าสู่ระบบ');
});

test('a registration link with an invalid token returns to registration with an error', function () {
    $registration = RegistrationToken::create([
        'email' => 'ada@example.com',
        'token_hash' => Hash::make('expected-token'),
        'expires_at' => now()->addMinutes(15),
    ]);
    $url = URL::temporarySignedRoute(
        'auth.registration.verify',
        now()->addMinutes(15),
        ['registration' => $registration->id, 'token' => 'wrong-token'],
    );

    $this->get($url)
        ->assertRedirectToRoute('auth.register')
        ->assertSessionHas('verification_link_error');

    $this->get(route('auth.register'))
        ->assertOk()
        ->assertSee('ลิงก์ยืนยันอีเมลไม่ถูกต้อง กรุณาสมัครสมาชิกใหม่อีกครั้ง');
});

test('an expired signed registration link does not expose a raw 403 error', function () {
    $registration = RegistrationToken::create([
        'email' => 'ada@example.com',
        'token_hash' => Hash::make('test-token'),
        'expires_at' => now()->addMinutes(15),
    ]);
    $url = URL::temporarySignedRoute(
        'auth.registration.verify',
        now()->subMinute(),
        ['registration' => $registration->id, 'token' => 'test-token'],
    );

    $this->get($url)
        ->assertRedirectToRoute('auth.register')
        ->assertSessionHas('verification_link_error');
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

test('a regular user is sent home when login was triggered by an admin URL', function () {
    $user = User::factory()->create(['password' => 'password123']);

    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('auth.login'));

    $this->post(route('auth.login.store'), [
        'email' => $user->email,
        'password' => 'password123',
    ])->assertRedirectToRoute('home');

    $this->assertAuthenticatedAs($user);
    $this->assertFalse(session()->has('url.intended'));
});

test('an admin keeps the intended admin URL after login', function () {
    $admin = User::factory()->admin()->create(['password' => 'password123']);

    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('auth.login'));

    $this->post(route('auth.login.store'), [
        'email' => $admin->email,
        'password' => 'password123',
    ])->assertRedirectToRoute('admin.dashboard');

    $this->assertAuthenticatedAs($admin);
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

    $this->get(route('auth.password-reset-sent', ['email' => 'other@example.com']))
        ->assertOk()
        ->assertSee($user->email)
        ->assertDontSee('other@example.com');
    $this->get(route('auth.password-reset-sent'))->assertOk();
});

test('password reset flow pages reject direct URLs and invalid tokens', function () {
    $this->get(route('auth.password-reset-sent'))
        ->assertRedirectToRoute('auth.forgot-password');
    $this->get(route('auth.password-reset-sent', ['email' => 'ada@example.com']))
        ->assertRedirectToRoute('auth.forgot-password');
    $this->get(route('password.reset', ['token' => 'made-up']))
        ->assertRedirectToRoute('auth.forgot-password')
        ->assertSessionHas('reset_link_error');

    $user = User::factory()->create(['email' => 'ada@example.com']);
    $token = Password::broker()->createToken($user);

    $this->get(route('password.reset', ['token' => $token, 'email' => 'other@example.com']))
        ->assertRedirectToRoute('auth.forgot-password');
    $this->get(route('password.reset', ['token' => 'invalid', 'email' => $user->email]))
        ->assertRedirectToRoute('auth.forgot-password');

    $this->travel(16)->minutes();
    $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))
        ->assertRedirectToRoute('auth.forgot-password');
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
    $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))
        ->assertRedirectToRoute('auth.forgot-password');
});

test('guests cannot access authenticated setup pages', function () {
    $this->get(route('auth.set-password'))
        ->assertRedirectToRoute('auth.register');

    $this->get(route('auth.set-username'))
        ->assertRedirectToRoute('auth.set-password');
});

test('the password recovery entry page is available', function () {
    $this->get(route('auth.forgot-password'))->assertOk();

    $this->get(route('auth.set-password'))->assertRedirectToRoute('auth.register');
});
