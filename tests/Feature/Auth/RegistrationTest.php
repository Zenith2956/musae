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

test('registration prevents SQL injection', function () {
    Role::factory()->create(['id' => 1, 'name' => 'élève']);

    $payload = [
        'name' => "Robert'); DROP TABLE users;--",
        'nickname' => "test'); DELETE FROM roles;--",
        'email' => "inject@example.com'); DROP TABLE roles;--",
        'password' => 'password',
        'password_confirmation' => 'password',
        'role_id' => 1,
    ];

    $response = $this->post(route('register.store'), $payload);

    $response->assertStatus(302);
    $response->assertSessionHasErrors(['email']);

    $this->assertDatabaseCount('users', 0);
    $this->assertDatabaseHas('roles', ['id' => 1]);
});

test('registration prevents XSS injection', function () {
    Role::factory()->create(['id' => 1, 'name' => 'élève']);

    $payload = [
        'name' => "<script>alert('XSS')</script>",
        'nickname' => "<img src=x onerror=alert(1)>",
        'email' => "xss@example.com",
        'password' => 'password',
        'password_confirmation' => 'password',
        'role_id' => 1,
    ];

    $response = $this->post(route('register.store'), $payload);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('users', ['email' => 'xss@example.com']);
});

test('registration works with CSRF token', function () {
    Role::factory()->create(['id' => 1, 'name' => 'élève']);

    $response = $this->withSession(['_token' => csrf_token()])
        ->post(route('register.store'), [
            '_token' => csrf_token(),
            'name' => 'Test User',
            'nickname' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role_id' => 1,
        ]);

    $response->assertStatus(302);
});



test('registration requires all mandatory fields', function () {
    Role::factory()->create(['id' => 1, 'name' => 'élève']);

    $response = $this->post(route('register.store'), []);

    $response->assertSessionHasErrors([
        'name',
        'nickname',
        'email',
        'password',
        'role_id',
    ]);
});


test('guest cannot access dashboard', function () {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});
