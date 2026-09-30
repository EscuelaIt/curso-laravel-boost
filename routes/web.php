<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ErrorTestController;
use App\Http\Controllers\FooController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\User\ShowUserPageController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::get('foo-js', [ErrorTestController::class, 'show'])->name('error-test');

Route::get('foo', FooController::class)->name('foo');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('messages', [MessageController::class, 'store'])->name('messages.store');
    Route::inertia('users', 'users/Index')->name('users.index');
    Route::get('users/{user}', ShowUserPageController::class)->whereNumber('user')->name('users.show');
});

require __DIR__.'/settings.php';
