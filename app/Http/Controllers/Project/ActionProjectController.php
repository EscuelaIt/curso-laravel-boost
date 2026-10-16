<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Services\ActionServices\ProjectActionService;
use EscuelaIT\APIKit\ActionHandler;
use Illuminate\Http\Response;

class ActionProjectController extends Controller
{
    use ActionHandler;

    /**
     * Execute the requested action on the given projects.
     */
    public function __invoke(ProjectActionService $projectActionService): Response
    {
        return $this->handleAction($projectActionService);
    }
}
