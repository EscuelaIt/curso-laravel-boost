<?php

use App\Models\Message;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('guests cannot execute user actions', function () {
    $this->postJson(route('api.users.action'), [])
        ->assertUnauthorized()
        ->assertJsonPath('status', 401);
});

test('a message is sent to every selected user', function () {
    $sender = User::factory()->create();
    $recipients = User::factory()->count(2)->create();
    Sanctum::actingAs($sender);

    $response = $this->postJson(route('api.users.action'), [
        'type' => 'UserMessageAction',
        'relatedIds' => $recipients->pluck('id')->all(),
        'data' => [
            'subject' => 'Hola equipo',
            'body' => 'Este es un mensaje de prueba.',
        ],
    ]);

    $response->assertOk()
        ->assertJsonPath('status', 200)
        ->assertJsonPath('data.action', 'UserMessageAction')
        ->assertJsonPath('data.data.sent_count', 2);

    foreach ($recipients as $recipient) {
        $this->assertDatabaseHas('messages', [
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'subject' => 'Hola equipo',
            'body' => 'Este es un mensaje de prueba.',
        ]);
    }
});

test('the sender is skipped when selected as a recipient', function () {
    $sender = User::factory()->create();
    $recipient = User::factory()->create();
    Sanctum::actingAs($sender);

    $this->postJson(route('api.users.action'), [
        'type' => 'UserMessageAction',
        'relatedIds' => [$sender->id, $recipient->id],
        'data' => ['subject' => 'Hola', 'body' => 'Mensaje para un solo usuario.'],
    ])->assertOk()->assertJsonPath('data.data.recipient_ids', [$recipient->id]);

    expect(Message::count())->toBe(1);
});

test('sending a message only to oneself fails', function () {
    $sender = User::factory()->create();
    Sanctum::actingAs($sender);

    $this->postJson(route('api.users.action'), [
        'type' => 'UserMessageAction',
        'relatedIds' => [$sender->id],
        'data' => ['subject' => 'Hola', 'body' => 'Mensaje para mí mismo.'],
    ])->assertUnprocessable()->assertJsonPath('message', 'No valid recipients selected.');

    expect(Message::count())->toBe(0);
});

test('the message action validates subject and body', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->postJson(route('api.users.action'), [
        'type' => 'UserMessageAction',
        'relatedIds' => [User::factory()->create()->id],
        'data' => ['subject' => '', 'body' => 'corto'],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['subject', 'body']);

    expect(Message::count())->toBe(0);
});

test('the selected users are deleted', function () {
    Sanctum::actingAs(User::factory()->create());
    $users = User::factory()->count(2)->create();

    $this->postJson(route('api.users.action'), [
        'type' => 'UserDeleteAction',
        'relatedIds' => $users->pluck('id')->all(),
        'data' => [],
    ])->assertOk()
        ->assertJsonPath('data.action', 'UserDeleteAction')
        ->assertJsonPath('data.data.deleted_count', 2);

    foreach ($users as $user) {
        $this->assertModelMissing($user);
    }
});

test('the authenticated user is skipped when selected for deletion', function () {
    $authUser = User::factory()->create();
    $user = User::factory()->create();
    Sanctum::actingAs($authUser);

    $this->postJson(route('api.users.action'), [
        'type' => 'UserDeleteAction',
        'relatedIds' => [$authUser->id, $user->id],
        'data' => [],
    ])->assertOk()->assertJsonPath('data.data.deleted_ids', [$user->id]);

    $this->assertModelExists($authUser);
    $this->assertModelMissing($user);
});

test('a user cannot delete only themselves', function () {
    $authUser = User::factory()->create();
    Sanctum::actingAs($authUser);

    $this->postJson(route('api.users.action'), [
        'type' => 'UserDeleteAction',
        'relatedIds' => [$authUser->id],
        'data' => [],
    ])->assertUnprocessable()->assertJsonPath('message', 'You cannot delete your own user.');

    $this->assertModelExists($authUser);
});
