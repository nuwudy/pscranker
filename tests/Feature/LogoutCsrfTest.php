<?php

use App\Models\User;

test('authenticated user can log out via POST without csrf token', function () {
    $user = User::factory()->create();

    // Act as user and hit POST /logout without CSRF token
    $response = $this->actingAs($user)->post('/logout');

    $response->assertStatus(302);
    $this->assertGuest();
});

test('authenticated user can log out via GET request', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/logout');

    $response->assertStatus(302);
    $this->assertGuest();
});

test('guest visiting logout route redirects gracefully without 419', function () {
    $response = $this->get('/logout');
    $response->assertStatus(302);

    $postResponse = $this->post('/logout');
    $postResponse->assertStatus(302);
});
