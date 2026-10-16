<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Inertia\Inertia;
use Inertia\Response;

class ShowProjectPageController extends Controller
{
    /**
     * Show the panel page with the detail of a project.
     *
     * Route model binding responds with a 404 when the project does not exist.
     * The project data is loaded from the API by the Lit component, so only its id is passed.
     */
    public function __invoke(Project $project): Response
    {
        return Inertia::render('projects/Show', [
            'projectId' => $project->id,
        ]);
    }
}
