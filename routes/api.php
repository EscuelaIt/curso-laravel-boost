<?php

use App\Http\Controllers\User\ListUserController;
use Illuminate\Support\Facades\Route;

Route::get('/users', [ListUserController::class, 'index'])->name('api.users.index');
