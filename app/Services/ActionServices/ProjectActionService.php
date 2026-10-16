<?php

namespace App\Services\ActionServices;

use App\Actions\Project\ProjectActivateAction;
use App\Actions\Project\ProjectDeactivateAction;
use App\Actions\Project\ProjectDeleteAction;
use App\Models\Project;
use EscuelaIT\APIKit\ActionService;

class ProjectActionService extends ActionService
{
    protected string $actionModel = Project::class;

    protected array $actionTypes = [
        'ProjectActivateAction' => ProjectActivateAction::class,
        'ProjectDeactivateAction' => ProjectDeactivateAction::class,
        'ProjectDeleteAction' => ProjectDeleteAction::class,
    ];
}
