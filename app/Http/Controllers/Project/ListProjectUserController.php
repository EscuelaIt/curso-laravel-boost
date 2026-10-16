<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Response;
use Negartarh\APIWrapper\Facades\APIResponse;

class ListProjectUserController extends Controller
{
    /**
     * List every user indicating whether it is associated with the given project.
     *
     * The items have the shape expected by the dile-relation-checker component.
     */
    public function index(Project $project): Response
    {
        $projectUserIds = $project->users()->pluck('users.id');

        $users = User::query()
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->map(fn (User $user): array => [
                'id' => $user->id,
                'label' => $user->name,
                'email' => $user->email,
                'checked' => $projectUserIds->contains($user->id),
            ]);

        return APIResponse::ok($users, 'Project users retrieved.');
    }
}
