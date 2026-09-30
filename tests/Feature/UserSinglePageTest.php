<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $this->get(route('users.show', 1))
        ->assertRedirect(route('login'));
});

test('authenticated users can visit the user single page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('users.show', $user))
        ->assertInertia(fn (Assert $page) => $page
            ->component('users/Show')
            ->where('userId', $user->id)
        );
});

test('the error page is rendered when the user does not exist', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('users.show', 999999))
        ->assertNotFound()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ErrorPage')
            ->where('status', 404)
        );
});
