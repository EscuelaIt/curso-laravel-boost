<?php

namespace App\Actions\User;

use App\Models\User;
use EscuelaIT\APIKit\ActionResult;
use EscuelaIT\APIKit\CrudAction;
use EscuelaIT\APIKit\Exceptions\UnauthenticatedActionException;

class UserMessageAction extends CrudAction
{
    /**
     * @return array<string, array<int, string>>
     */
    protected function validationRules(): array
    {
        return [
            'subject' => ['required', 'string', 'min:3', 'max:255'],
            'body' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    /**
     * Send a message from the authenticated user to every selected user except themselves.
     */
    public function handle(): ActionResult
    {
        if (! $this->user) {
            throw new UnauthenticatedActionException;
        }

        $recipientIds = $this->models
            ->reject(fn (User $recipient): bool => $recipient->is($this->user))
            ->each(fn (User $recipient) => $this->user->sentMessages()->create([
                'recipient_id' => $recipient->id,
                'subject' => $this->data['subject'],
                'body' => $this->data['body'],
            ]))
            ->pluck('id')
            ->values()
            ->all();

        if ($recipientIds === []) {
            return $this->createActionResultError('No valid recipients selected.');
        }

        $sentCount = count($recipientIds);

        return $this->createActionResultSuccess(
            $sentCount === 1 ? 'Message sent to 1 user.' : "Message sent to {$sentCount} users.",
            [
                'sent_count' => $sentCount,
                'recipient_ids' => $recipientIds,
            ]
        );
    }
}
