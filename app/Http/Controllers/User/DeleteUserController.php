<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\DeleteUserRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Negartarh\APIWrapper\Facades\APIResponse;

class DeleteUserController extends Controller
{
    /**
     * Remove the given user.
     */
    public function destroy(DeleteUserRequest $request, User $user): Response
    {
        $user->delete();

        return APIResponse::ok($user, 'User deleted.');
    }
}
