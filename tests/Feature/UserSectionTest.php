<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $this->get(route('users.index'))
        ->assertRedirect(route('login'));
});

test('authenticated users can visit the users section', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('users.index'))
        ->assertInertia(fn (Assert $page) => $page->component('users/Index'));
});
