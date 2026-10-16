<?php

namespace App\Actions\Project;

use App\Models\Project;
use EscuelaIT\APIKit\ActionResult;
use EscuelaIT\APIKit\CrudAction;

class ProjectDeactivateAction extends CrudAction
{
    /**
     * Deactivate every selected project that is currently active.
     */
    public function handle(): ActionResult
    {
        $deactivatedIds = $this->models
            ->filter(fn (Project $project): bool => $project->is_active)
            ->each(fn (Project $project) => $project->update(['is_active' => false]))
            ->pluck('id')
            ->values()
            ->all();

        if ($deactivatedIds === []) {
            return $this->createActionResultError('The project is already inactive.');
        }

        $deactivatedCount = count($deactivatedIds);

        return $this->createActionResultSuccess(
            $deactivatedCount === 1 ? 'Project deactivated.' : "{$deactivatedCount} projects deactivated.",
            [
                'deactivated_count' => $deactivatedCount,
                'deactivated_ids' => $deactivatedIds,
            ]
        );
    }
}
