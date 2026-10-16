<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\Response;
use Negartarh\APIWrapper\Facades\APIResponse;

class UpdateProjectController extends Controller
{
    /**
     * Update the given project.
     */
    public function update(UpdateProjectRequest $request, Project $project): Response
    {
        $project->update($request->validated());

        return APIResponse::ok($project, 'Project updated.');
    }
}
