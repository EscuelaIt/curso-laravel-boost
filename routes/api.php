<?php

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
});
