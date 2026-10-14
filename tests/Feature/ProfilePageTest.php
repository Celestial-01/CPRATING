<?php

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
