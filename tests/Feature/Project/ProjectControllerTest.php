<?php

use App\Models\Project;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('guests cannot manage projects', function (string $method, string $routeName) {
    $project = Project::factory()->create();

    $this->json($method, route($routeName, $project))
        ->assertUnauthorized()
        ->assertJsonPath('status', 401)
        ->assertJsonPath('message', 'Unauthorized');
})->with([
    'store' => ['POST', 'api.projects.store'],
    'show' => ['GET', 'api.projects.show'],
    'update' => ['PUT', 'api.projects.update'],
    'destroy' => ['DELETE', 'api.projects.destroy'],
]);

test('a project can be created', function () {
    Sanctum::actingAs(User::factory()->create());

    $response = $this->postJson(route('api.projects.store'), [
        'name' => 'Intranet corporativa',
        'description' => 'Portal para empleados',
        'start_date' => '2026-01-15',
        'end_date' => '2026-06-30',
        'is_active' => false,
    ]);

    $response->assertCreated()
        ->assertJsonPath('status', 201)
        ->assertJsonPath('message', 'Project created.')
        ->assertJsonPath('data.name', 'Intranet corporativa')
        ->assertJsonPath('data.start_date', '2026-01-15')
        ->assertJsonPath('data.end_date', '2026-06-30')
        ->assertJsonPath('data.is_active', false);

    $this->assertDatabaseHas('projects', [
        'name' => 'Intranet corporativa',
        'description' => 'Portal para empleados',
        'is_active' => false,
    ]);
});

test('a project is active by default and can omit the optional fields', function () {
    Sanctum::actingAs(User::factory()->create());

    $response = $this->postJson(route('api.projects.store'), [
        'name' => 'Intranet corporativa',
        'start_date' => '2026-01-15',
        'end_date' => null,
        'description' => null,
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.is_active', true)
        ->assertJsonPath('data.end_date', null)
        ->assertJsonPath('data.description', null);
});

test('creating a project requires name and start date', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->postJson(route('api.projects.store'), [])
        ->assertUnprocessable()
        ->assertJsonPath('status', 422)
        ->assertJsonPath('message', 'Unprocessable Entity')
        ->assertJsonValidationErrors([
            'name' => 'The name field is required.',
            'start_date' => 'The start date field is required.',
        ]);

    expect(Project::count())->toBe(0);
});

test('creating a project rejects an end date before the start date', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->postJson(route('api.projects.store'), [
        'name' => 'Intranet corporativa',
        'start_date' => '2026-01-15',
        'end_date' => '2026-01-14',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'end_date' => 'The end date field must be a date after or equal to start date.',
        ]);

    expect(Project::count())->toBe(0);
});

test('creating a project rejects dates with an invalid format', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->postJson(route('api.projects.store'), [
        'name' => 'Intranet corporativa',
        'start_date' => '15/01/2026',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'start_date' => 'The start date field must match the format Y-m-d.',
        ]);

    expect(Project::count())->toBe(0);
});

test('a project can be retrieved', function () {
    Sanctum::actingAs(User::factory()->create());
    $project = Project::factory()->create(['start_date' => '2026-01-15']);

    $this->getJson(route('api.projects.show', $project))
        ->assertOk()
        ->assertJsonPath('message', 'Project retrieved.')
        ->assertJsonPath('data.id', $project->id)
        ->assertJsonPath('data.start_date', '2026-01-15');
});

test('retrieving a missing project returns not found', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->getJson(route('api.projects.show', 999))
        ->assertNotFound()
        ->assertJsonPath('status', 404)
        ->assertJsonPath('message', 'Not Found');
});

test('a project can be updated', function () {
    Sanctum::actingAs(User::factory()->create());
    $project = Project::factory()->create();

    $this->putJson(route('api.projects.update', $project), [
        'name' => 'Nuevo nombre',
        'description' => null,
        'start_date' => '2026-02-01',
        'end_date' => null,
        'is_active' => false,
    ])
        ->assertOk()
        ->assertJsonPath('message', 'Project updated.')
        ->assertJsonPath('data.name', 'Nuevo nombre');

    $project->refresh();
    expect($project->start_date->toDateString())->toBe('2026-02-01')
        ->and($project->end_date)->toBeNull()
        ->and($project->description)->toBeNull()
        ->and($project->is_active)->toBeFalse();
});

test('updating a project rejects invalid data', function () {
    Sanctum::actingAs(User::factory()->create());
    $project = Project::factory()->create(['name' => 'Nombre original']);

    $this->putJson(route('api.projects.update', $project), [
        'name' => '',
        'start_date' => '2026-02-01',
        'end_date' => '2026-01-01',
    ])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Unprocessable Entity')
        ->assertJsonValidationErrors(['name', 'end_date']);

    expect($project->refresh()->name)->toBe('Nombre original');
});

test('deleting a project removes its relations with users', function () {
    Sanctum::actingAs(User::factory()->create());
    $project = Project::factory()->hasAttached(User::factory()->count(2))->create();

    $this->deleteJson(route('api.projects.destroy', $project))
        ->assertOk()
        ->assertJsonPath('message', 'Project deleted.')
        ->assertJsonPath('data.id', $project->id);

    $this->assertSoftDeleted($project);
    $this->assertDatabaseMissing('project_user', ['project_id' => $project->id]);
});

test('deleting a missing project returns not found', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->deleteJson(route('api.projects.destroy', 999))
        ->assertNotFound()
        ->assertJsonPath('status', 404);
});
