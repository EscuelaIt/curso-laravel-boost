<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Negartarh\APIWrapper\Facades\APIResponse;

class StoreUserController extends Controller
{
    /**
     * Store a newly created user with a random password.
     */
    public function store(StoreUserRequest $request): Response
    {
        $user = User::create([
            ...$request->validated(),
            'password' => Str::password(),
        ]);

        return APIResponse::created($user, 'User created.');
    }
}
