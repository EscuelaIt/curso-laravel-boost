<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Negartarh\APIWrapper\Facades\APIResponse;

class UpdateUserController extends Controller
{
    /**
     * Update the given user.
     */
    public function update(UpdateUserRequest $request, User $user): Response
    {
        $user->update($request->validated());

        return APIResponse::ok($user, 'User updated.');
    }
}
