<?php

use App\Models\Project;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('guests cannot execute project actions', function () {
    $this->postJson(route('api.projects.action'), [])
        ->assertUnauthorized()
        ->assertJsonPath('status', 401);
});

test('an inactive project is activated', function () {
    Sanctum::actingAs(User::factory()->create());
    $project = Project::factory()->inactive()->create();

    $this->postJson(route('api.projects.action'), [
        'type' => 'ProjectActivateAction',
        'relatedIds' => [$project->id],
        'data' => [],
    ])->assertOk()
        ->assertJsonPath('message', 'Project activated.')
        ->assertJsonPath('data.action', 'ProjectActivateAction')
        ->assertJsonPath('data.data.activated_ids', [$project->id]);

    expect($project->refresh()->is_active)->toBeTrue();
});

test('activating a project that is already active fails', function () {
    Sanctum::actingAs(User::factory()->create());
    $project = Project::factory()->create();

    $this->postJson(route('api.projects.action'), [
        'type' => 'ProjectActivateAction',
        'relatedIds' => [$project->id],
        'data' => [],
    ])->assertUnprocessable()
        ->assertJsonPath('message', 'The project is already active.');
});

test('an active project is deactivated', function () {
    Sanctum::actingAs(User::factory()->create());
    $project = Project::factory()->create();

    $this->postJson(route('api.projects.action'), [
        'type' => 'ProjectDeactivateAction',
        'relatedIds' => [$project->id],
        'data' => [],
    ])->assertOk()
        ->assertJsonPath('message', 'Project deactivated.')
        ->assertJsonPath('data.action', 'ProjectDeactivateAction')
        ->assertJsonPath('data.data.deactivated_ids', [$project->id]);

    expect($project->refresh()->is_active)->toBeFalse();
});

test('deactivating a project that is already inactive fails', function () {
    Sanctum::actingAs(User::factory()->create());
    $project = Project::factory()->inactive()->create();

    $this->postJson(route('api.projects.action'), [
        'type' => 'ProjectDeactivateAction',
        'relatedIds' => [$project->id],
        'data' => [],
    ])->assertUnprocessable()
        ->assertJsonPath('message', 'The project is already inactive.');
});

test('the selected projects are deleted along with their relations with users', function () {
    Sanctum::actingAs(User::factory()->create());
    $projects = Project::factory()->count(2)->hasAttached(User::factory())->create();
    $otherProject = Project::factory()->hasAttached(User::factory())->create();

    $this->postJson(route('api.projects.action'), [
        'type' => 'ProjectDeleteAction',
        'relatedIds' => $projects->pluck('id')->all(),
        'data' => [],
    ])->assertOk()
        ->assertJsonPath('message', '2 projects deleted.')
        ->assertJsonPath('data.action', 'ProjectDeleteAction')
        ->assertJsonPath('data.data.deleted_count', 2);

    foreach ($projects as $project) {
        $this->assertSoftDeleted($project);
        $this->assertDatabaseMissing('project_user', ['project_id' => $project->id]);
    }

    $this->assertNotSoftDeleted($otherProject);
    $this->assertDatabaseHas('project_user', ['project_id' => $otherProject->id]);
});

test('deleting projects that do not exist fails', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->postJson(route('api.projects.action'), [
        'type' => 'ProjectDeleteAction',
        'relatedIds' => [999],
        'data' => [],
    ])->assertUnprocessable()
        ->assertJsonPath('message', 'No projects selected.');
});
