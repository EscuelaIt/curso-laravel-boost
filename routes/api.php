<?php

use App\Http\Controllers\Project\ActionProjectController;
use App\Http\Controllers\Project\DeleteProjectController;
use App\Http\Controllers\Project\ListProjectController;
use App\Http\Controllers\Project\ListProjectUserController;
use App\Http\Controllers\Project\ShowProjectController;
use App\Http\Controllers\Project\StoreProjectController;
use App\Http\Controllers\Project\ToggleProjectUserController;
use App\Http\Controllers\Project\UpdateProjectController;
use App\Http\Controllers\User\ActionUserController;
use App\Http\Controllers\User\DeleteUserController;
use App\Http\Controllers\User\ListUserController;
use App\Http\Controllers\User\ShowUserController;
use App\Http\Controllers\User\StoreUserController;
use App\Http\Controllers\User\UpdateUserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->name('api.')->group(function () {
    Route::get('/users', [ListUserController::class, 'index'])->name('users.index');
    Route::post('/users', [StoreUserController::class, 'store'])->name('users.store');
    Route::post('/users/action', ActionUserController::class)->name('users.action');
    Route::get('/users/{user}', [ShowUserController::class, 'show'])->name('users.show');
    Route::match(['put', 'patch'], '/users/{user}', [UpdateUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [DeleteUserController::class, 'destroy'])->name('users.destroy');

    Route::get('/projects', [ListProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [StoreProjectController::class, 'store'])->name('projects.store');
    Route::post('/projects/action', ActionProjectController::class)->name('projects.action');
    Route::get('/projects/{project}', [ShowProjectController::class, 'show'])->name('projects.show');
    Route::match(['put', 'patch'], '/projects/{project}', [UpdateProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [DeleteProjectController::class, 'destroy'])->name('projects.destroy');
    Route::get('/projects/{project}/users', [ListProjectUserController::class, 'index'])->name('projects.users.index');
    Route::post('/projects/{project}/users', [ToggleProjectUserController::class, 'update'])->name('projects.users.update');
});
