<?php

use App\Mail\RegistrationVerificationMail;
use App\Models\Course;
use App\Models\RegistrationToken;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;
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
        ->assertSee('data-home-nav-link', false)
        ->assertSee('data-home-hero', false)
        ->assertSee('CPRATING');
});

test('catalog and study plan pages are available', function () {
    Course::factory()->create(['course_code' => 'CP422301']);
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
    Course::factory()->create(['course_code' => 'CP422301', 'major' => 'CY']);
    Course::factory()->create(['course_code' => 'CP422305', 'major' => 'CY']);
    $this->get(route('courses.show', 'CP422301'))
        ->assertOk()
        ->assertViewIs('courses.show')
        ->assertSee('CP422301')
        ->assertSee('data-related-courses', false)
        ->assertSee('class="course-card"', false)
        ->assertSee('ความคิดเห็น');
});

test('an unknown course code returns not found', function () {
    $this->get(route('courses.show', 'CP000000'))->assertNotFound();
});

test('login and registration pages are available', function () {
    $this->get(route('auth.login'))
        ->assertOk()
        ->assertViewIs('auth.login')
        ->assertSee('src="'.asset('images/cprating-logo.svg').'"', false)
        ->assertSee('data-auth-entry', false)
        ->assertSee('data-active="login"', false)
        ->assertSee('data-auth-tab="register"', false)
        ->assertSee('data-auth-email-input', false)
        ->assertDontSee('data-google-error-dialog', false);

    $this->get(route('auth.register'))
        ->assertOk()
        ->assertViewIs('auth.register')
        ->assertSee('data-auth-entry', false)
        ->assertSee('data-active="register"', false)
        ->assertSee('data-auth-tab="login"', false)
        ->assertSee('data-auth-email-input', false)
        ->assertSee('src="'.asset('images/cprating-logo.svg').'"', false);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('src="'.asset('images/cprating-logo.svg').'"', false);
});

test('missing auth fields use clear Thai validation messages', function () {
    $this->from(route('auth.login'))
        ->post(route('auth.login.store'), [])
        ->assertRedirect(route('auth.login'))
        ->assertSessionHasErrors([
            'email' => 'กรุณากรอกอีเมล',
            'password' => 'กรุณากรอกรหัสผ่าน',
        ]);

    $this->from(route('auth.register'))
        ->post(route('auth.register.store'), [])
        ->assertRedirect(route('auth.register'))
        ->assertSessionHasErrors(['email' => 'กรุณากรอกอีเมล']);
});

test('an authenticated navbar displays the user name, email, and Google avatar', function () {
    $user = User::factory()->create([
        'name' => 'Patiphan',
        'email' => 'patiphan@kkumail.com',
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
        ->assertSee('patiphan@kkumail.com')
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

test('a regular user cannot see the admin link and is sent home when opening the dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertDontSee(route('admin.dashboard'), false);

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertRedirectToRoute('home');

    $this->assertAuthenticatedAs($user);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('data-user-menu', false);
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

test('a regular user is sent home when opening an admin course page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/admin/courses/')
        ->assertRedirectToRoute('home');

    $this->assertAuthenticatedAs($user);
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
        'email' => 'ada@kkumail.com',
    ]);

    $response->assertRedirectToRoute('auth.verify-email');
    $response->assertSessionHas('registration_email', 'ada@kkumail.com');
    $this->assertGuest();
    $this->assertDatabaseHas('registration_tokens', ['email' => 'ada@kkumail.com']);
    Mail::assertSent(RegistrationVerificationMail::class, function (RegistrationVerificationMail $mail) {
        $html = $mail->render();

        return $mail->hasTo('ada@kkumail.com')
            && str_contains($html, 'ยืนยันอีเมลของคุณ')
            && str_contains($html, 'ลิงก์นี้จะหมดอายุภายใน 15 นาที')
            && str_contains($html, 'ยืนยันอีเมล');
    });
});

test('registration rejects addresses outside kkumail before sending mail', function () {
    Mail::fake();

    $this->from(route('auth.register'))
        ->post(route('auth.register.store'), ['email' => 'ada@gmail.com'])
        ->assertRedirect(route('auth.register'))
        ->assertSessionHasErrors('email');

    $this->assertDatabaseCount('registration_tokens', 0);
    Mail::assertNothingSent();
});

test('registration normalizes the case of a kkumail address', function () {
    Mail::fake();

    $this->post(route('auth.register.store'), ['email' => 'Ada@KKUMAIL.COM'])
        ->assertRedirectToRoute('auth.verify-email')
        ->assertSessionHas('registration_email', 'ada@kkumail.com');

    Mail::assertSent(RegistrationVerificationMail::class, fn (RegistrationVerificationMail $mail) => $mail->hasTo('ada@kkumail.com'));
});

test('Google sign-in rejects a non-kkumail account without creating a user', function () {
    $provider = Mockery::mock();
    $provider->shouldReceive('user')->once()->andReturn(GoogleUser::fake([
        'email' => 'ada@gmail.com',
        'email_verified' => true,
    ]));
    Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

    $this->get(route('auth.google.callback'))
        ->assertRedirectToRoute('auth.login')
        ->assertSessionHas('google_error');

    $this->assertGuest();
    $this->assertDatabaseCount('users', 0);
    $this->assertDatabaseCount('social_accounts', 0);
});

test('canceling Google sign-in returns to the auth layout with a retry link', function () {
    $this->get(route('auth.google.callback', ['error' => 'access_denied']))
        ->assertRedirectToRoute('auth.login')
        ->assertSessionHas('google_error');

    $this->get(route('auth.login'))
        ->assertOk()
        ->assertSee('การดำเนินการด้วย Google ถูกยกเลิก')
        ->assertSee('data-google-error-dialog', false)
        ->assertSee('data-google-error-close', false)
        ->assertSee('href="'.route('auth.google.redirect').'"', false);

    $this->assertGuest();
});

test('a failed Google callback returns to login without exposing the provider error', function () {
    $provider = Mockery::mock();
    $provider->shouldReceive('user')->once()->andThrow(new RuntimeException('OAuth transport secret'));
    Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

    $this->get(route('auth.google.callback'))
        ->assertRedirectToRoute('auth.login')
        ->assertSessionHas('google_error');

    $this->get(route('auth.login'))
        ->assertSee('เชื่อมต่อ Google ไม่สำเร็จ')
        ->assertDontSee('OAuth transport secret');

    $this->assertGuest();
    $this->assertDatabaseCount('users', 0);
});

test('Google sign-in requires a verified kkumail address and stores its avatar', function () {
    $provider = Mockery::mock();
    $provider->shouldReceive('user')->once()->andReturn(GoogleUser::fake([
        'email' => 'ada@kkumail.com',
        'name' => 'Ada Lovelace',
        'avatar' => 'https://example.com/ada.jpg',
        'email_verified' => true,
    ]));
    Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

    $this->get(route('auth.google.callback'))->assertRedirectToRoute('profile.complete');

    $user = User::query()->where('email', 'ada@kkumail.com')->firstOrFail();
    $this->assertAuthenticatedAs($user);
    expect($user->avatarUrl())->toBe('https://example.com/ada.jpg');
});

test('a verified Google sign-in connects its avatar to an existing email account', function () {
    $user = User::factory()->create([
        'name' => 'Chosen Name',
        'email' => 'ada@kkumail.com',
    ]);

    $provider = Mockery::mock();
    $provider->shouldReceive('user')->once()->andReturn(GoogleUser::fake([
        'email' => $user->email,
        'name' => 'Google Name',
        'avatar' => 'https://example.com/ada.jpg',
        'email_verified' => true,
    ]));
    Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

    $this->get(route('auth.google.callback'))->assertRedirectToRoute('home');

    $this->assertAuthenticatedAs($user);
    $this->assertDatabaseCount('users', 1);
    expect($user->fresh()->name)->toBe('Chosen Name');
    expect($user->fresh()->avatarUrl())->toBe('https://example.com/ada.jpg');
});

test('Google sign-in rejects a kkumail address that Google has not verified', function () {
    $provider = Mockery::mock();
    $provider->shouldReceive('user')->once()->andReturn(GoogleUser::fake([
        'email' => 'ada@kkumail.com',
        'email_verified' => false,
    ]));
    Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

    $this->get(route('auth.google.callback'))
        ->assertRedirectToRoute('auth.login')
        ->assertSessionHas('google_error');

    $this->assertGuest();
    $this->assertDatabaseCount('users', 0);
});

test('a registration whose stored email cannot be sent to fails with a Thai validation error instead of a crash', function () {

    Mail::shouldReceive('to')->once()->andThrow(
        new RfcComplianceException('Email "c" does not comply with addr-spec of RFC 2822.'),
    );

    $this->from(route('auth.register'))
        ->post(route('auth.register.store'), ['email' => 'ada@kkumail.com'])
        ->assertRedirect(route('auth.register'))
        ->assertSessionHasErrors(['email' => 'ไม่สามารถส่งอีเมลยืนยันไปยังอีเมลนี้ได้ กรุณาตรวจสอบอีเมลแล้วลองใหม่อีกครั้ง']);

    expect(RegistrationToken::query()->where('email', 'ada@kkumail.com')->exists())->toBeFalse();
});

test('a mail transport failure keeps registration on the auth page and leaves no token', function () {
    Mail::shouldReceive('to')->once()->andThrow(new RuntimeException('SMTP credentials secret'));

    $this->from(route('auth.register'))
        ->post(route('auth.register.store'), ['email' => 'ada@kkumail.com'])
        ->assertRedirect(route('auth.register'))
        ->assertSessionHasErrors(['email' => 'ส่งลิงก์ยืนยันไม่สำเร็จ กรุณาลองอีกครั้งในภายหลัง']);

    $this->assertDatabaseCount('registration_tokens', 0);
});

test('a failed resend preserves the previous verification token', function () {
    $registration = RegistrationToken::create([
        'email' => 'ada@kkumail.com',
        'token_hash' => Hash::make('original-token'),
        'expires_at' => now()->addMinutes(10),
    ]);
    $originalHash = $registration->token_hash;

    $this->withSession([
        'pending_registration_id' => $registration->id,
        'registration_email' => $registration->email,
    ]);

    Mail::shouldReceive('to')->once()->andThrow(new RuntimeException('SMTP unavailable'));

    $this->post(route('auth.verify-email.resend'))
        ->assertRedirectToRoute('auth.verify-email')
        ->assertSessionHas('verification_resend_error');

    $this->get(route('auth.verify-email'))
        ->assertOk()
        ->assertSee('ส่งลิงก์ยืนยันไม่สำเร็จ')
        ->assertSee('ส่งลิงก์ยืนยันอีกครั้ง');

    expect($registration->fresh()->token_hash)->toBe($originalHash);
    expect($registration->fresh()->expires_at->isFuture())->toBeTrue();
});

test('a user can resend the registration verification email', function () {
    Mail::fake();

    $this->post(route('auth.register.store'), ['email' => 'ada@kkumail.com']);

    $this->post(route('auth.verify-email.resend'), ['email' => 'other@kkumail.com'])
        ->assertRedirectToRoute('auth.verify-email')
        ->assertSessionHas('verification_resent', true);

    Mail::assertSent(RegistrationVerificationMail::class, 2);
    Mail::assertNotSent(RegistrationVerificationMail::class, fn (RegistrationVerificationMail $mail) => $mail->hasTo('other@kkumail.com'));
});

test('registration verification resend is rate limited with a friendly response', function () {
    Mail::fake();
    $this->post(route('auth.register.store'), ['email' => 'throttle@kkumail.com']);

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
    $this->get(route('auth.verify-email', ['email' => 'ada@kkumail.com']))
        ->assertRedirectToRoute('auth.register');
    $this->post(route('auth.verify-email.resend'), ['email' => 'ada@kkumail.com'])
        ->assertRedirectToRoute('auth.register');
    Mail::assertNothingSent();

    $this->post(route('auth.register.store'), ['email' => 'ada@kkumail.com']);

    $this->get(route('auth.verify-email', ['email' => 'other@kkumail.com']))
        ->assertOk()
        ->assertSee('ada@kkumail.com')
        ->assertDontSee('other@kkumail.com');
    $this->get(route('auth.verify-email'))->assertOk();
});

test('an expired registration link can be resent from the pending page', function () {
    Mail::fake();
    $this->post(route('auth.register.store'), ['email' => 'ada@kkumail.com']);
    RegistrationToken::query()->where('email', 'ada@kkumail.com')->update([
        'expires_at' => now()->subMinute(),
    ]);

    $this->get(route('auth.verify-email'))->assertOk();
    $this->post(route('auth.verify-email.resend'))
        ->assertRedirectToRoute('auth.verify-email');

    expect(RegistrationToken::query()->where('email', 'ada@kkumail.com')->first()->expires_at->isFuture())
        ->toBeTrue();
});

test('a user completes registration after verifying the email', function () {
    $registration = RegistrationToken::create([
        'email' => 'ada@kkumail.com',
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
        ->assertRedirectToRoute('profile.complete');

    $this->post(route('profile.complete.store'), [
        'student_id' => '690000001-1',
        'major_code' => 'CS',
    ])->assertRedirectToRoute('profile.complete.success');

    $this->get(route('profile.complete.success'))
        ->assertOk()
        ->assertViewIs('profile.success')
        ->assertSee('KKU 63')
        ->assertSee('CS')
        ->assertSee('href="'.route('home').'"', false)
        ->assertSee('กลับหน้าหลัก')
        ->assertDontSee('เริ่มค้นหารายวิชา')
        ->assertDontSee('href="'.route('courses.index').'"', false);

    $this->get(route('home'))->assertOk();

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => 'ada@kkumail.com',
        'name' => 'Ada Lovelace',
        'student_id' => '690000001-1',
        'major_code' => 'CS',
    ]);

    $this->post(route('auth.logout'))->assertRedirectToRoute('home');
    $this->get(route('auth.verify-email'))->assertRedirectToRoute('auth.register');
});

test('registration does not create an account until a password and name are provided', function () {
    $registration = RegistrationToken::create([
        'email' => 'ada@kkumail.com',
        'token_hash' => Hash::make('test-token'),
        'expires_at' => now()->addMinutes(15),
    ]);

    $this->withSession(['registration_token_id' => $registration->id])
        ->post(route('auth.set-password.store'), [])
        ->assertSessionHasErrors('password');

    $this->post(route('auth.set-password.store'), [
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirectToRoute('auth.set-username');

    $this->post(route('auth.set-username.store'), ['name' => ''])
        ->assertSessionHasErrors('name');

    $this->assertDatabaseCount('users', 0);
});

test('registration returns to email verification when its session is lost', function () {
    $this->get(route('auth.set-password'))
        ->assertRedirectToRoute('auth.register')
        ->assertSessionHas('verification_link_error');

    $this->get(route('auth.set-username'))
        ->assertRedirectToRoute('auth.register')
        ->assertSessionHas('verification_link_error');

    $this->assertDatabaseCount('users', 0);
});

test('registration asks for a new password when only the password step is lost', function () {
    $registration = RegistrationToken::create([
        'email' => 'ada@kkumail.com',
        'token_hash' => Hash::make('test-token'),
        'expires_at' => now()->addMinutes(15),
    ]);

    $this->withSession(['registration_token_id' => $registration->id])
        ->get(route('auth.set-username'))
        ->assertRedirectToRoute('auth.set-password')
        ->assertSessionHasErrors('password');

    $this->get(route('auth.set-password'))->assertOk();
    $this->assertDatabaseCount('users', 0);
});

test('an incomplete profile is redirected to the required student information form', function () {
    $user = User::factory()->create([
        'student_id' => null,
        'major_code' => null,
    ]);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertRedirectToRoute('profile.complete');

    $this->actingAs($user)
        ->get(route('profile.complete'))
        ->assertOk()
        ->assertViewIs('profile.complete')
        ->assertSee('name="student_id"', false)
        ->assertSee('name="major_code"', false);

    $this->actingAs($user)
        ->post(route('profile.complete.store'), [
            'student_id' => '690000002-2',
            'major_code' => 'AI',
        ])
        ->assertRedirectToRoute('profile.complete.success');

    expect($user->fresh()->profileTags())->toBe(['kku63', 'AI']);
});

test('signing in again resumes an incomplete student profile', function () {
    $user = User::factory()->create([
        'password' => 'password123',
        'student_id' => null,
        'major_code' => null,
    ]);

    $this->post(route('auth.login.store'), [
        'email' => $user->email,
        'password' => 'password123',
    ])->assertRedirectToRoute('profile.complete');

    $this->get(route('profile.complete'))
        ->assertOk()
        ->assertViewIs('profile.complete');
});

test('profile completion identifies the account source correctly', function () {
    $emailUser = User::factory()->create([
        'student_id' => null,
        'major_code' => null,
    ]);

    $this->actingAs($emailUser)
        ->get(route('profile.complete'))
        ->assertSee('อีเมลที่ยืนยันแล้ว');

    $googleUser = User::factory()->create([
        'student_id' => null,
        'major_code' => null,
    ]);

    SocialAccount::create([
        'user_id' => $googleUser->id,
        'provider' => 'google',
        'provider_user_id' => 'profile-google-user',
    ]);

    $this->actingAs($googleUser)
        ->get(route('profile.complete'))
        ->assertSee('จาก Google');
});

test('an admin bypasses the profile completion flow', function () {
    $admin = User::factory()->admin()->create([
        'student_id' => null,
        'major_code' => null,
    ]);

    $this->actingAs($admin)
        ->get(route('profile.complete'))
        ->assertRedirectToRoute('admin.dashboard');

    $this->actingAs($admin)
        ->post(route('profile.complete.store'), [
            'student_id' => '690000003-3',
            'major_code' => 'CS',
        ])
        ->assertRedirectToRoute('admin.dashboard');
});

test('an expired registration link returns to the verification page with a resend error', function () {
    $registration = RegistrationToken::create([
        'email' => 'ada@kkumail.com',
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
        'email' => 'ada@kkumail.com',
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
        'email' => 'ada@kkumail.com',
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
        'email' => 'ada@kkumail.com',
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

test('a registration link whose token record is gone returns to registration', function () {
    $url = URL::temporarySignedRoute(
        'auth.registration.verify',
        now()->addMinutes(15),
        ['registration' => 99999, 'token' => 'missing-token'],
    );

    $this->get($url)
        ->assertRedirectToRoute('auth.register')
        ->assertSessionHas('verification_link_error');

    $this->get(route('auth.register'))
        ->assertOk()
        ->assertSee('ไม่พบลิงก์ยืนยันนี้แล้ว');
});

test('a malformed auth link renders the safe auth error page', function () {
    $this->get('/reset-password')
        ->assertStatus(405)
        ->assertViewIs('auth.error-recovery')
        ->assertSee('auth-scene', false)
        ->assertSee('กลับไปขอลิงก์ใหม่')
        ->assertDontSee('Stack trace');
});

test('an expired auth session renders a retry page instead of a raw 419 error', function () {
    Route::get('/auth/google/expired-session', fn () => abort(419, 'CSRF internal detail'));

    $this->get('/auth/google/expired-session')
        ->assertStatus(419)
        ->assertViewIs('auth.error-recovery')
        ->assertSee('auth-scene', false)
        ->assertSee('หน้านี้หมดอายุแล้ว')
        ->assertSee('กลับไปเริ่มใหม่')
        ->assertDontSee('CSRF internal detail');
});

test('an unexpected auth failure renders a safe page without exception details', function () {
    Route::get('/auth/google/broken-service', fn () => throw new RuntimeException('provider secret detail'));

    $this->get('/auth/google/broken-service')
        ->assertStatus(500)
        ->assertViewIs('auth.error-recovery')
        ->assertSee('auth-scene', false)
        ->assertSee('กลับไปลองอีกครั้ง')
        ->assertDontSee('provider secret detail');
});

test('a stale registration form cannot replace an existing password', function () {
    $user = User::factory()->create([
        'email' => 'ada@kkumail.com',
        'password' => 'original-password',
    ]);
    $registration = RegistrationToken::create([
        'email' => $user->email,
        'token_hash' => Hash::make('test-token'),
        'expires_at' => now()->addMinutes(15),
    ]);

    $this->withSession([
        'registration_token_id' => $registration->id,
        'registration_password' => Hash::make('replacement-password'),
    ]);

    $this->post(route('auth.set-username.store'), ['name' => 'Replacement Name'])
        ->assertRedirectToRoute('auth.login')
        ->assertSessionHas('status');

    $this->assertGuest();
    expect(Hash::check('original-password', $user->fresh()->password))->toBeTrue();
    expect($user->fresh()->name)->not->toBe('Replacement Name');
});

test('a Google-only account can complete email registration with the same email', function () {
    $user = User::factory()->create([
        'email' => 'ada@kkumail.com',
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

    $this->post(route('auth.logout'))->assertRedirectToRoute('home');
    $this->assertGuest();

    $this->get(route('home'))
        ->assertOk()
        ->assertSee(route('auth.login'), false);
});

test('login rejects an existing account outside kkumail', function () {
    $user = User::factory()->create([
        'email' => 'ada@gmail.com',
        'password' => 'password123',
    ]);

    $this->from(route('auth.login'))
        ->post(route('auth.login.store'), [
            'email' => $user->email,
            'password' => 'password123',
        ])
        ->assertRedirect(route('auth.login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('an account without a Google avatar gets a stable initial and color', function () {
    $user = User::factory()->create([
        'name' => '123 Patiphan',
        'email' => 'patiphan@kkumail.com',
    ]);

    expect($user->avatarInitial())->toBe('P');
    expect($user->avatarColorClasses())->toBe($user->fresh()->avatarColorClasses());

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertSee($user->avatarColorClasses())
        ->assertSee('data-mobile-profile-toggle', false);
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

test('a regular user receives forbidden for a JSON admin request', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->getJson(route('admin.dashboard'))
        ->assertForbidden();

    $this->assertAuthenticatedAs($user);
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
    $user = User::factory()->create(['email' => 'ada@kkumail.com']);

    $response = $this->post(route('auth.forgot-password.store'), [
        'email' => $user->email,
    ]);

    $response
        ->assertRedirectToRoute('auth.password-reset-sent')
        ->assertSessionHas('password_reset_email', $user->email);
    Notification::assertSentTo($user, ResetPassword::class);

    $this->get(route('auth.password-reset-sent', ['email' => 'other@kkumail.com']))
        ->assertOk()
        ->assertSee($user->email)
        ->assertDontSee('other@kkumail.com');
    $this->get(route('auth.password-reset-sent'))->assertOk();
});

test('a password reset mail failure returns to the form without claiming delivery', function () {
    $user = User::factory()->create(['email' => 'ada@kkumail.com']);
    Notification::shouldReceive('send')->once()->andThrow(new RuntimeException('SMTP unavailable'));

    $this->from(route('auth.forgot-password'))
        ->post(route('auth.forgot-password.store'), ['email' => $user->email])
        ->assertRedirect(route('auth.forgot-password'))
        ->assertSessionHas('reset_link_error');

    $this->get(route('auth.forgot-password'))
        ->assertOk()
        ->assertSee('ส่งลิงก์รีเซ็ตรหัสผ่านไม่สำเร็จ');

    $this->assertDatabaseCount('password_reset_tokens', 0);
});

test('password reset flow pages reject direct URLs and invalid tokens', function () {
    $this->get(route('auth.password-reset-sent'))
        ->assertRedirectToRoute('auth.forgot-password');
    $this->get(route('auth.password-reset-sent', ['email' => 'ada@kkumail.com']))
        ->assertRedirectToRoute('auth.forgot-password');
    $this->get(route('password.reset', ['token' => 'made-up']))
        ->assertRedirectToRoute('auth.forgot-password')
        ->assertSessionHas('reset_link_error');

    $user = User::factory()->create(['email' => 'ada@kkumail.com']);
    $token = Password::broker()->createToken($user);

    $this->get(route('password.reset', ['token' => $token, 'email' => 'other@kkumail.com']))
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

test('setup pages require an active registration session', function () {
    $this->get(route('auth.set-password'))
        ->assertRedirectToRoute('auth.register');

    $this->get(route('auth.set-username'))
        ->assertRedirectToRoute('auth.register');
});

test('the password recovery entry page is available', function () {
    $this->get(route('auth.forgot-password'))->assertOk();

    $this->get(route('auth.set-password'))->assertRedirectToRoute('auth.register');
});

test('an unexpected auth error renders a safe retry page in the auth layout', function () {
    $provider = Mockery::mock();
    $provider->shouldReceive('redirect')->once()->andThrow(new RuntimeException('provider configuration secret'));
    Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

    $this->get(route('auth.google.redirect'))
        ->assertRedirectToRoute('auth.login')
        ->assertSessionHas('google_error');

    $this->get(route('auth.login'))
        ->assertOk()
        ->assertSee('ยังเชื่อมต่อ Google ไม่ได้')
        ->assertDontSee('provider configuration secret');
});
