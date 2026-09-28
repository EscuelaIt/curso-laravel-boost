<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Response;
use Negartarh\APIWrapper\Facades\APIResponse;

class ShowUserController extends Controller
{
    /**
     * Display the given user.
     */
    public function show(User $user): Response
    {
        return APIResponse::ok($user, 'User retrieved.');
    }
}
