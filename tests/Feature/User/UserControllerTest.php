<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

test('guests cannot manage users', function (string $method, string $routeName) {
    $user = User::factory()->create();

    $this->json($method, route($routeName, $user))
        ->assertUnauthorized()
        ->assertJsonPath('status', 401)
        ->assertJsonPath('message', 'Unauthorized');
})->with([
    'store' => ['POST', 'api.users.store'],
    'show' => ['GET', 'api.users.show'],
    'update' => ['PUT', 'api.users.update'],
    'destroy' => ['DELETE', 'api.users.destroy'],
]);

test('a user can be created with a random password', function () {
    Sanctum::actingAs(User::factory()->create());

    $response = $this->postJson(route('api.users.store'), [
        'name' => 'Ana Lopez',
        'email' => 'ana@example.com',
        'password' => 'secret-password',
        'is_favorite' => true,
    ]);

    $response->assertCreated()
        ->assertJsonPath('status', 201)
        ->assertJsonPath('message', 'User created.')
        ->assertJsonPath('data.email', 'ana@example.com')
        ->assertJsonMissingPath('data.password');

    $createdUser = User::firstWhere('email', 'ana@example.com');
    expect($createdUser->name)->toBe('Ana Lopez')
        ->and($createdUser->is_favorite)->toBeTrue()
        ->and($createdUser->password)->not->toBeEmpty()
        ->and(Hash::check('secret-password', $createdUser->password))->toBeFalse();
});

test('creating a user requires name and email', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->postJson(route('api.users.store'), [])
        ->assertUnprocessable()
        ->assertJsonPath('status', 422)
        ->assertJsonPath('message', 'Unprocessable Entity')
        ->assertJsonValidationErrors([
            'name' => 'The name field is required.',
            'email' => 'The email field is required.',
        ]);

    expect(User::count())->toBe(1);
});

test('creating a user requires a unique email', function () {
    Sanctum::actingAs(User::factory()->create(['email' => 'taken@example.com']));

    $this->postJson(route('api.users.store'), [
        'name' => 'Ana Lopez',
        'email' => 'taken@example.com',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email' => 'The email has already been taken.']);
});

test('a user can be retrieved', function () {
    Sanctum::actingAs(User::factory()->create());
    $user = User::factory()->create();

    $this->getJson(route('api.users.show', $user))
        ->assertOk()
        ->assertJsonPath('message', 'User retrieved.')
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonMissingPath('data.password');
});

test('retrieving a missing user returns not found', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->getJson(route('api.users.show', 999))
        ->assertNotFound()
        ->assertJsonPath('status', 404)
        ->assertJsonPath('message', 'Not Found');
});

test('a user can be updated without changing the password', function () {
    Sanctum::actingAs(User::factory()->create());
    $user = User::factory()->create();
    $originalPassword = $user->password;

    $this->putJson(route('api.users.update', $user), [
        'name' => 'Nuevo Nombre',
        'email' => 'nuevo@example.com',
        'password' => 'new-secret-password',
        'is_favorite' => true,
    ])
        ->assertOk()
        ->assertJsonPath('message', 'User updated.')
        ->assertJsonPath('data.name', 'Nuevo Nombre');

    $user->refresh();
    expect($user->email)->toBe('nuevo@example.com')
        ->and($user->is_favorite)->toBeTrue()
        ->and($user->password)->toBe($originalPassword);
});

test('a user can keep their own email when updated', function () {
    Sanctum::actingAs(User::factory()->create());
    $user = User::factory()->create(['email' => 'same@example.com']);

    $this->putJson(route('api.users.update', $user), [
        'name' => 'Nuevo Nombre',
        'email' => 'same@example.com',
    ])->assertOk();
});

test('updating a user rejects an email taken by another user', function () {
    Sanctum::actingAs(User::factory()->create(['email' => 'taken@example.com']));
    $user = User::factory()->create();

    $this->putJson(route('api.users.update', $user), [
        'name' => $user->name,
        'email' => 'taken@example.com',
    ])
        ->assertUnprocessable()
        ->assertJsonPath('status', 422)
        ->assertJsonPath('message', 'Unprocessable Entity')
        ->assertJsonValidationErrors(['email' => 'The email has already been taken.']);
});

test('a user can be deleted', function () {
    Sanctum::actingAs(User::factory()->create());
    $user = User::factory()->create();

    $this->deleteJson(route('api.users.destroy', $user))
        ->assertOk()
        ->assertJsonPath('message', 'User deleted.')
        ->assertJsonPath('data.id', $user->id);

    $this->assertModelMissing($user);
});

test('a user cannot delete themselves', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->deleteJson(route('api.users.destroy', $user))
        ->assertForbidden()
        ->assertJsonPath('status', 403)
        ->assertJsonPath('message', 'You cannot delete your own user.');

    $this->assertModelExists($user);
});
