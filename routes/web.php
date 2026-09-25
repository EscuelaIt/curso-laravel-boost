<?php

use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
});

require __DIR__.'/settings.php';
