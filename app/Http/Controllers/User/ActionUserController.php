<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\ActionServices\UserActionService;
use EscuelaIT\APIKit\ActionHandler;
use Illuminate\Http\Response;

class ActionUserController extends Controller
{
    use ActionHandler;

    /**
     * Execute the requested action on the given users.
     */
    public function __invoke(UserActionService $userActionService): Response
    {
        return $this->handleAction($userActionService);
    }
}
