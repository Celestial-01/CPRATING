<?php

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

test('guests must sign in and incomplete users must finish onboarding before viewing their profile', function () {
    $this->get(route('profile.show'))->assertRedirect(route('auth.login'));

    $user = User::factory()->create([
        'student_id' => null,
        'major_code' => null,
    ]);

    $this->actingAs($user)
        ->get(route('profile.show'))
        ->assertRedirectToRoute('profile.complete');
});

test('an email account sees its own profile and the profile link in both navigation menus', function () {
    $user = User::factory()->create([
        'name' => 'Patiphan',
        'email' => 'patiphan@kkumail.com',
        'student_id' => '690000001-1',
        'major_code' => 'CS',
    ]);

    $this->actingAs($user)
        ->get(route('profile.show'))
        ->assertOk()
        ->assertViewIs('profile.show')
        ->assertSee('Patiphan')
        ->assertSee('patiphan@kkumail.com')
        ->assertSee('690000001-1')
        ->assertSee('Computer Science')
        ->assertSee('ชื่อที่คุณตั้งไว้และอีเมลที่ใช้สมัครสมาชิกแสดงอยู่ที่นี่')
        ->assertSee('href="'.route('profile.show').'"', false)
        ->assertSee('data-mobile-menu-link', false)
        ->assertDontSee(route('admin.dashboard'), false);
});

test('a Google account sees its avatar and the correct account source', function () {
    $user = User::factory()->create();

    SocialAccount::create([
        'user_id' => $user->id,
        'provider' => 'google',
        'provider_user_id' => 'profile-google-user',
        'avatar_url' => 'https://example.com/avatar.jpg',
    ]);

    $this->actingAs($user)
        ->get(route('profile.show'))
        ->assertOk()
        ->assertSee('https://example.com/avatar.jpg')
        ->assertSee('เชื่อมต่อกับ Google')
        ->assertSee('ชื่อ รูปโปรไฟล์ และอีเมลเชื่อมต่อกับบัญชี Google');
});

test('an admin can view a profile without student information and retains the admin link', function () {
    $admin = User::factory()->admin()->create([
        'student_id' => null,
        'major_code' => null,
    ]);

    $this->actingAs($admin)
        ->get(route('profile.show'))
        ->assertOk()
        ->assertSee(route('admin.dashboard'), false)
        ->assertDontSee('id="student-information-title"', false);
});

test('profile completion only stores validated student fields', function () {
    $user = User::factory()->create([
        'student_id' => null,
        'major_code' => null,
    ]);

    $this->actingAs($user)->post(route('profile.complete.store'), [
        'student_id' => '690000001-1',
        'major_code' => 'CS',
        'role' => 'admin',
        'email' => 'other@kkumail.com',
    ])->assertRedirectToRoute('profile.complete.success');

    $user->refresh();
    expect($user->student_id)->toBe('690000001-1')
        ->and($user->major_code)->toBe('CS')
        ->and($user->role)->toBe('user')
        ->and($user->email)->not->toBe('other@kkumail.com');
});

test('profile completion requires both student ID and major', function () {
    $user = User::factory()->create([
        'student_id' => null,
        'major_code' => null,
    ]);

    $this->actingAs($user)
        ->from(route('profile.complete'))
        ->post(route('profile.complete.store'), [])
        ->assertRedirectToRoute('profile.complete')
        ->assertSessionHasErrors(['student_id', 'major_code']);

    expect($user->fresh()->hasCompletedProfile())->toBeFalse();

    $this->get(route('home'))->assertRedirectToRoute('profile.complete');
});

test('an expired student form gives guests a login recovery link', function () {
    Route::post('/profile/complete', fn () => abort(419, 'CSRF internal detail'));

    $this->post(route('profile.complete.store'))
        ->assertStatus(419)
        ->assertViewIs('auth.error-recovery')
        ->assertSee('href="'.route('auth.login').'"', false)
        ->assertSee('เข้าสู่ระบบอีกครั้ง')
        ->assertDontSee('CSRF internal detail');
});

test('an expired student form lets authenticated users reopen the form', function () {
    $user = User::factory()->create([
        'student_id' => null,
        'major_code' => null,
    ]);
    Route::post('/profile/complete', fn () => abort(419, 'CSRF internal detail'));

    $this->actingAs($user)
        ->post(route('profile.complete.store'))
        ->assertStatus(419)
        ->assertViewIs('auth.error-recovery')
        ->assertSee('href="'.route('profile.complete').'"', false)
        ->assertSee('กลับไปกรอกข้อมูล')
        ->assertDontSee('CSRF internal detail');
});

test('admins are redirected away from student profile completion before validation', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post(route('profile.complete.store'), [])
        ->assertRedirectToRoute('admin.dashboard')
        ->assertSessionHasNoErrors();
});
