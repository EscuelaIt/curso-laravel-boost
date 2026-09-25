<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $this->get(route('messages.index'))->assertRedirect(route('login'));
});

test('authenticated users see the other users of the app', function () {
    $user = User::factory()->create();
    $otherUsers = User::factory()->count(2)->create();

    $this->actingAs($user)
        ->get(route('messages.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('messages/Index')
            ->has('users', 2)
            ->where('users', fn ($users) => $users->pluck('id')->sort()->values()->all() === $otherUsers->pluck('id')->sort()->values()->all())
        );
});
