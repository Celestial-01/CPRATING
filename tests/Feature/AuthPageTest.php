<?php

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

test('authentication flow pages are available', function () {
    collect([
        'auth.verify-email',
        'auth.set-password',
        'auth.set-username',
        'auth.forgot-password',
        'auth.password-reset-sent',
        'auth.reset-password',
    ])->each(function (string $routeName) {
        $this->get(route($routeName))->assertOk();
    });
});
