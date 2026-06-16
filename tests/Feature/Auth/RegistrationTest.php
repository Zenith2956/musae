<?php

use App\Models\Role;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyFeature(Features::registration());
});


test('new users can register', function () {
    Role::factory()->create(['id' => 1, 'name' => 'élève']);

    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'nickname' => 'testuser',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role_id' => 1,
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard'));
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});
