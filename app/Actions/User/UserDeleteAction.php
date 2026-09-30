<?php

namespace App\Actions\User;

use App\Models\User;
use EscuelaIT\APIKit\ActionResult;
use EscuelaIT\APIKit\CrudAction;
use EscuelaIT\APIKit\Exceptions\UnauthenticatedActionException;

class UserDeleteAction extends CrudAction
{
    /**
     * Delete every selected user except the authenticated user, who cannot delete themselves.
     */
    public function handle(): ActionResult
    {
        if (! $this->user) {
            throw new UnauthenticatedActionException;
        }

        $deletedIds = $this->models
            ->reject(fn (User $user): bool => $user->is($this->user))
            ->each(fn (User $user) => $user->delete())
            ->pluck('id')
            ->values()
            ->all();

        if ($deletedIds === []) {
            return $this->createActionResultError('You cannot delete your own user.');
        }

        $deletedCount = count($deletedIds);

        return $this->createActionResultSuccess(
            $deletedCount === 1 ? '1 user deleted.' : "{$deletedCount} users deleted.",
            [
                'deleted_count' => $deletedCount,
                'deleted_ids' => $deletedIds,
            ]
        );
    }
}
