<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $this->get(route('projects.index'))
        ->assertRedirect(route('login'));
});

test('authenticated users can visit the projects section', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('projects.index'))
        ->assertInertia(fn (Assert $page) => $page->component('projects/Index'));
});
