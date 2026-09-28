<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Negartarh\APIWrapper\Facades\APIResponse;

class StoreUserController extends Controller
{
    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): Response
    {
        $user = User::create($request->validated());

        return APIResponse::created($user, 'User created.');
    }
}
