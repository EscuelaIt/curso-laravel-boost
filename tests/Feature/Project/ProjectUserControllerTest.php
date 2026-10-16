<?php

use App\Models\Project;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('guests cannot manage the users of a project', function (string $method, string $routeName) {
    $project = Project::factory()->create();

    $this->json($method, route($routeName, $project))
        ->assertUnauthorized()
        ->assertJsonPath('status', 401);
})->with([
    'index' => ['GET', 'api.projects.users.index'],
    'update' => ['POST', 'api.projects.users.update'],
]);

test('every user is listed indicating whether it belongs to the project', function () {
    $member = User::factory()->create(['name' => 'Ana', 'email' => 'ana@example.com']);
    $outsider = User::factory()->create(['name' => 'Bruno', 'email' => 'bruno@example.com']);
    Sanctum::actingAs($member);
    $project = Project::factory()->hasAttached($member)->create();
    Project::factory()->hasAttached($outsider)->create();

    $this->getJson(route('api.projects.users.index', $project))
        ->assertOk()
        ->assertJsonPath('message', 'Project users retrieved.')
        ->assertJsonPath('data', [
            ['id' => $member->id, 'label' => 'Ana', 'email' => 'ana@example.com', 'checked' => true],
            ['id' => $outsider->id, 'label' => 'Bruno', 'email' => 'bruno@example.com', 'checked' => false],
        ]);
});

test('listing the users of a missing project returns not found', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->getJson(route('api.projects.users.index', 999))
        ->assertNotFound()
        ->assertJsonPath('status', 404);
});

test('a user can be added to a project', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $project = Project::factory()->create();

    $this->postJson(route('api.projects.users.update', $project), ['id' => $user->id, 'checked' => true])
        ->assertOk()
        ->assertJsonPath('message', 'User added to the project.')
        ->assertJsonPath('data.checked', true);

    $this->assertDatabaseHas('project_user', ['project_id' => $project->id, 'user_id' => $user->id]);
});

test('adding a user that already belongs to the project does not duplicate the relation', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $project = Project::factory()->hasAttached($user)->create();

    $this->postJson(route('api.projects.users.update', $project), ['id' => $user->id, 'checked' => true])
        ->assertOk();

    $this->assertDatabaseCount('project_user', 1);
});

test('a user can be removed from a project', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    Sanctum::actingAs($user);
    $project = Project::factory()->hasAttached([$user, $otherUser])->create();

    $this->postJson(route('api.projects.users.update', $project), ['id' => $user->id, 'checked' => false])
        ->assertOk()
        ->assertJsonPath('message', 'User removed from the project.')
        ->assertJsonPath('data.checked', false);

    $this->assertDatabaseMissing('project_user', ['project_id' => $project->id, 'user_id' => $user->id]);
    $this->assertDatabaseHas('project_user', ['project_id' => $project->id, 'user_id' => $otherUser->id]);
});

test('toggling a project user requires an existing user and the checked flag', function () {
    Sanctum::actingAs(User::factory()->create());
    $project = Project::factory()->create();

    $this->postJson(route('api.projects.users.update', $project), ['id' => 999])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Unprocessable Entity')
        ->assertJsonValidationErrors([
            'id' => 'The selected id is invalid.',
            'checked' => 'The checked field is required.',
        ]);

    $this->assertDatabaseCount('project_user', 0);
});

test('deleting a user removes its relations with projects', function () {
    Sanctum::actingAs(User::factory()->create());
    $user = User::factory()->create();
    $project = Project::factory()->hasAttached($user)->create();

    $this->deleteJson(route('api.users.destroy', $user))->assertOk();

    $this->assertDatabaseMissing('project_user', ['project_id' => $project->id]);
});
