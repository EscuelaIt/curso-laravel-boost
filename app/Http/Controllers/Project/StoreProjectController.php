<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Models\Project;
use Illuminate\Http\Response;
use Negartarh\APIWrapper\Facades\APIResponse;

class StoreProjectController extends Controller
{
    /**
     * Store a newly created project.
     */
    public function store(StoreProjectRequest $request): Response
    {
        $project = Project::create($request->validated())->refresh();

        return APIResponse::created($project, 'Project created.');
    }
}
