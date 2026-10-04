<?php

test('guests are redirected to the login page', function () {
    $response = $this->get('/');

    $response->assertRedirectToRoute('auth.login');
});

test('login and registration pages are available', function () {
    $this->get(route('auth.login'))
        ->assertOk()
        ->assertViewIs('auth.login');

    $this->get(route('auth.register'))
        ->assertOk()
        ->assertViewIs('auth.register');
});
