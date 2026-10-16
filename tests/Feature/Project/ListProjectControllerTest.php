<?php

use App\Models\Project;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('guests cannot list projects', function () {
    $this->getJson(route('api.projects.index'))
        ->assertUnauthorized()
        ->assertJsonPath('status', 401)
        ->assertJsonPath('message', 'Unauthorized');
});

test('authenticated users can list projects', function () {
    Sanctum::actingAs(User::factory()->create());
    Project::factory()->count(2)->create();

    $response = $this->getJson(route('api.projects.index'));

    $response->assertOk()
        ->assertJsonPath('data.countItems', 2)
        ->assertJsonCount(2, 'data.result.data');
});

test('deleted projects are not listed', function () {
    Sanctum::actingAs(User::factory()->create());
    $project = Project::factory()->create();
    Project::factory()->create()->delete();

    $response = $this->getJson(route('api.projects.index'));

    $response->assertOk()
        ->assertJsonCount(1, 'data.result.data')
        ->assertJsonPath('data.result.data.0.id', $project->id);
});

test('keyword search matches projects by name or description', function (string $keyword) {
    Sanctum::actingAs(User::factory()->create());
    $matchingProject = Project::factory()->create(['name' => 'Intranet corporativa', 'description' => 'Portal para empleados']);
    Project::factory()->create(['name' => 'Tienda online', 'description' => 'Venta de cursos']);

    $response = $this->getJson(route('api.projects.index', ['keyword' => $keyword]));

    $response->assertOk()
        ->assertJsonCount(1, 'data.result.data')
        ->assertJsonPath('data.result.data.0.id', $matchingProject->id);
})->with([
    'name' => ['intranet'],
    'description' => ['empleados'],
]);

test('projects can be filtered to only active ones', function () {
    Sanctum::actingAs(User::factory()->create());
    $activeProject = Project::factory()->create();
    Project::factory()->inactive()->create();

    $response = $this->getJson(route('api.projects.index', [
        'filters' => [['name' => 'is_active', 'active' => 'true', 'value' => 'true']],
    ]));

    $response->assertOk()
        ->assertJsonCount(1, 'data.result.data')
        ->assertJsonPath('data.result.data.0.id', $activeProject->id);
});

test('the active filter is ignored when it is not enabled', function () {
    Sanctum::actingAs(User::factory()->create());
    Project::factory()->create();
    Project::factory()->inactive()->create();

    $response = $this->getJson(route('api.projects.index', [
        'filters' => [['name' => 'is_active', 'active' => 'false', 'value' => 'false']],
    ]));

    $response->assertOk()
        ->assertJsonCount(2, 'data.result.data');
});

test('filters on columns other than is_active are ignored', function () {
    Sanctum::actingAs(User::factory()->create());
    Project::factory()->create(['name' => 'Alfa']);
    Project::factory()->create(['name' => 'Beta']);

    $response = $this->getJson(route('api.projects.index', [
        'filters' => [['name' => 'name', 'active' => 'true', 'value' => 'Alfa']],
    ]));

    $response->assertOk()
        ->assertJsonCount(2, 'data.result.data');
});

test('projects can be sorted by name', function () {
    Sanctum::actingAs(User::factory()->create());
    Project::factory()->create(['name' => 'Beta']);
    Project::factory()->create(['name' => 'Alfa']);

    $response = $this->getJson(route('api.projects.index', [
        'sortField' => 'name',
        'sortDirection' => 'asc',
    ]));

    $response->assertOk()
        ->assertJsonPath('data.result.data.0.name', 'Alfa')
        ->assertJsonPath('data.result.data.1.name', 'Beta');
});

test('projects can be sorted by start date', function () {
    Sanctum::actingAs(User::factory()->create());
    Project::factory()->create(['name' => 'Antiguo', 'start_date' => '2025-01-10', 'end_date' => null]);
    Project::factory()->create(['name' => 'Reciente', 'start_date' => '2026-03-20', 'end_date' => null]);

    $response = $this->getJson(route('api.projects.index', [
        'sortField' => 'start_date',
        'sortDirection' => 'desc',
    ]));

    $response->assertOk()
        ->assertJsonPath('data.result.data.0.name', 'Reciente')
        ->assertJsonPath('data.result.data.1.name', 'Antiguo');
});

test('sorting by an unsupported field is ignored', function () {
    Sanctum::actingAs(User::factory()->create());
    Project::factory()->count(2)->create();

    $response = $this->getJson(route('api.projects.index', [
        'sortField' => 'unknown_column',
        'sortDirection' => 'sideways',
    ]));

    $response->assertOk()
        ->assertJsonCount(2, 'data.result.data');
});
