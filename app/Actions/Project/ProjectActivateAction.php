<?php

namespace App\Actions\Project;

use App\Models\Project;
use EscuelaIT\APIKit\ActionResult;
use EscuelaIT\APIKit\CrudAction;

class ProjectActivateAction extends CrudAction
{
    /**
     * Activate every selected project that is not active yet.
     */
    public function handle(): ActionResult
    {
        $activatedIds = $this->models
            ->reject(fn (Project $project): bool => $project->is_active)
            ->each(fn (Project $project) => $project->update(['is_active' => true]))
            ->pluck('id')
            ->values()
            ->all();

        if ($activatedIds === []) {
            return $this->createActionResultError('The project is already active.');
        }

        $activatedCount = count($activatedIds);

        return $this->createActionResultSuccess(
            $activatedCount === 1 ? 'Project activated.' : "{$activatedCount} projects activated.",
            [
                'activated_count' => $activatedCount,
                'activated_ids' => $activatedIds,
            ]
        );
    }
}
