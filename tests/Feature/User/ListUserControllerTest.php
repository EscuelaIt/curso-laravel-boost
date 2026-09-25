<?php

use App\Models\User;

test('guests can list users', function () {
    User::factory()->count(3)->create();

    $response = $this->getJson(route('api.users.index'));

    $response->assertOk()
        ->assertJsonPath('data.countItems', 3)
        ->assertJsonCount(3, 'data.result.data');
});

test('keyword search matches users by name', function () {
    $matchingUser = User::factory()->create(['name' => 'Miguel Angel', 'email' => 'first@example.com']);
    User::factory()->create(['name' => 'Laura Perez', 'email' => 'second@example.com']);

    $response = $this->getJson(route('api.users.index', ['keyword' => 'miguel']));

    $response->assertOk()
        ->assertJsonCount(1, 'data.result.data')
        ->assertJsonPath('data.result.data.0.id', $matchingUser->id);
});

test('keyword search matches users by email', function () {
    $matchingUser = User::factory()->create(['name' => 'Miguel Angel', 'email' => 'contacto@escuela.it']);
    User::factory()->create(['name' => 'Laura Perez', 'email' => 'laura@example.com']);

    $response = $this->getJson(route('api.users.index', ['keyword' => 'escuela']));

    $response->assertOk()
        ->assertJsonCount(1, 'data.result.data')
        ->assertJsonPath('data.result.data.0.id', $matchingUser->id);
});

test('keyword search returns no users when nothing matches', function () {
    User::factory()->create(['name' => 'Miguel Angel', 'email' => 'contacto@escuela.it']);

    $response = $this->getJson(route('api.users.index', ['keyword' => 'inexistente']));

    $response->assertOk()
        ->assertJsonPath('data.countItems', 0)
        ->assertJsonCount(0, 'data.result.data');
});

test('the listing does not expose sensitive user attributes', function () {
    User::factory()->create();

    $response = $this->getJson(route('api.users.index'));

    $response->assertOk()
        ->assertJsonMissingPath('data.result.data.0.password')
        ->assertJsonMissingPath('data.result.data.0.remember_token')
        ->assertJsonMissingPath('data.result.data.0.two_factor_secret');
});

test('users can be filtered to only favorites', function () {
    $favoriteUser = User::factory()->favorite()->create();
    User::factory()->create();

    $response = $this->getJson(route('api.users.index', [
        'filters' => [['name' => 'is_favorite', 'active' => 'true', 'value' => 'true']],
    ]));

    $response->assertOk()
        ->assertJsonCount(1, 'data.result.data')
        ->assertJsonPath('data.result.data.0.id', $favoriteUser->id);
});

test('users can be filtered to only non favorites', function () {
    User::factory()->favorite()->create();
    $regularUser = User::factory()->create();

    $response = $this->getJson(route('api.users.index', [
        'filters' => [['name' => 'is_favorite', 'active' => 'true', 'value' => 'false']],
    ]));

    $response->assertOk()
        ->assertJsonCount(1, 'data.result.data')
        ->assertJsonPath('data.result.data.0.id', $regularUser->id);
});

test('filters on columns other than is_favorite are ignored', function () {
    User::factory()->create(['name' => 'Ana']);
    User::factory()->create(['name' => 'Bruno']);

    $response = $this->getJson(route('api.users.index', [
        'filters' => [['name' => 'name', 'active' => 'true', 'value' => 'Ana']],
    ]));

    $response->assertOk()
        ->assertJsonCount(2, 'data.result.data');
});

test('users can be filtered by email domain', function () {
    $matchingUser = User::factory()->create(['email' => 'ana@example.com']);
    User::factory()->create(['email' => 'bruno@escuela.it']);
    User::factory()->create(['email' => 'carla@example.com.mx']);
    User::factory()->create(['email' => 'dani@notexample.com']);

    $response = $this->getJson(route('api.users.index', [
        'filters' => [['name' => 'email_domain', 'active' => 'true', 'value' => 'example.com']],
    ]));

    $response->assertOk()
        ->assertJsonCount(1, 'data.result.data')
        ->assertJsonPath('data.result.data.0.id', $matchingUser->id);
});

test('the email domain filter is ignored when it is not active', function () {
    User::factory()->create(['email' => 'ana@example.com']);
    User::factory()->create(['email' => 'bruno@escuela.it']);

    $response = $this->getJson(route('api.users.index', [
        'filters' => [['name' => 'email_domain', 'active' => 'false', 'value' => 'example.com']],
    ]));

    $response->assertOk()
        ->assertJsonCount(2, 'data.result.data');
});
