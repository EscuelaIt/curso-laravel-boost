<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Response;
use Negartarh\APIWrapper\Facades\APIResponse;

class ShowProjectController extends Controller
{
    /**
     * Display the given project.
     */
    public function show(Project $project): Response
    {
        return APIResponse::ok($project, 'Project retrieved.');
    }
}
