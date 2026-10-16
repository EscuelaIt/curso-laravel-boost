<?php

namespace App\Actions\Project;

use App\Models\Project;
use EscuelaIT\APIKit\ActionResult;
use EscuelaIT\APIKit\CrudAction;

class ProjectDeleteAction extends CrudAction
{
    /**
     * Delete every selected project along with its relations with users.
     */
    public function handle(): ActionResult
    {
        $deletedIds = $this->models
            ->each(fn (Project $project) => $project->delete())
            ->pluck('id')
            ->values()
            ->all();

        if ($deletedIds === []) {
            return $this->createActionResultError('No projects selected.');
        }

        $deletedCount = count($deletedIds);

        return $this->createActionResultSuccess(
            $deletedCount === 1 ? '1 project deleted.' : "{$deletedCount} projects deleted.",
            [
                'deleted_count' => $deletedCount,
                'deleted_ids' => $deletedIds,
            ]
        );
    }
}
