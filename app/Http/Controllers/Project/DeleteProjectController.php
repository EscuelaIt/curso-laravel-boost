<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\DeleteProjectRequest;
use App\Models\Project;
use Illuminate\Http\Response;
use Negartarh\APIWrapper\Facades\APIResponse;

class DeleteProjectController extends Controller
{
    /**
     * Remove the given project along with its relations with users.
     */
    public function destroy(DeleteProjectRequest $request, Project $project): Response
    {
        $project->delete();

        return APIResponse::ok($project, 'Project deleted.');
    }
}
