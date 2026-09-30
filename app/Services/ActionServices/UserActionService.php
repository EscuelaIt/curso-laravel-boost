<?php

namespace App\Services\ActionServices;

use App\Actions\User\UserDeleteAction;
use App\Actions\User\UserMessageAction;
use App\Models\User;
use EscuelaIT\APIKit\ActionService;

class UserActionService extends ActionService
{
    protected string $actionModel = User::class;

    protected array $actionTypes = [
        'UserDeleteAction' => UserDeleteAction::class,
        'UserMessageAction' => UserMessageAction::class,
    ];
}
