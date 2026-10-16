<?php

use App\Models\Project;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $this->get(route('projects.show', 1))
        ->assertRedirect(route('login'));
});

test('authenticated users can visit the project single page', function () {
    $project = Project::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('projects.show', $project))
        ->assertInertia(fn (Assert $page) => $page
            ->component('projects/Show')
            ->where('projectId', $project->id)
        );
});

test('the error page is rendered when the project does not exist', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('projects.show', 999999))
        ->assertNotFound()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ErrorPage')
            ->where('status', 404)
        );
});
