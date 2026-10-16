<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\ToggleProjectUserRequest;
use App\Models\Project;
use Illuminate\Http\Response;
use Negartarh\APIWrapper\Facades\APIResponse;

class ToggleProjectUserController extends Controller
{
    /**
     * Associate or dissociate a user and the given project.
     */
    public function update(ToggleProjectUserRequest $request, Project $project): Response
    {
        $userId = $request->integer('id');

        if ($request->boolean('checked')) {
            $project->users()->syncWithoutDetaching([$userId]);

            return APIResponse::ok(['id' => $userId, 'checked' => true], 'User added to the project.');
        }

        $project->users()->detach($userId);

        return APIResponse::ok(['id' => $userId, 'checked' => false], 'User removed from the project.');
    }
}
