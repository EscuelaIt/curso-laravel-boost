<?php

use App\Models\Message;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $this->get(route('messages.index'))->assertRedirect(route('login'));
});

test('authenticated users see the other users of the app', function () {
    $user = User::factory()->create();
    $otherUsers = User::factory()->count(2)->create();

    $this->actingAs($user)
        ->get(route('messages.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('messages/Index')
            ->has('users', 2)
            ->where('users', fn ($users) => $users->pluck('id')->sort()->values()->all() === $otherUsers->pluck('id')->sort()->values()->all())
        );
});

test('guests cannot send messages', function () {
    $recipient = User::factory()->create();

    $this->post(route('messages.store'), [
        'recipient_id' => $recipient->id,
        'subject' => 'Hello',
        'body' => 'How are you?',
    ])->assertRedirect(route('login'));

    expect(Message::count())->toBe(0);
});

test('authenticated users can send a message to another user', function () {
    $sender = User::factory()->create();
    $recipient = User::factory()->create();

    $this->actingAs($sender)
        ->post(route('messages.store'), [
            'recipient_id' => $recipient->id,
            'subject' => 'Hello',
            'body' => 'How are you?',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('messages.index'));

    $message = Message::sole();

    expect($message->sender_id)->toBe($sender->id)
        ->and($message->recipient_id)->toBe($recipient->id)
        ->and($message->subject)->toBe('Hello')
        ->and($message->body)->toBe('How are you?')
        ->and($message->isRead())->toBeFalse();
});

test('users cannot send a message to themselves', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('messages.store'), [
            'recipient_id' => $user->id,
            'subject' => 'Hello',
            'body' => 'How are you?',
        ])
        ->assertSessionHasErrors('recipient_id');

    expect(Message::count())->toBe(0);
});

test('sending a message requires a valid recipient, subject and body', function (array $payload, string $invalidField) {
    $user = User::factory()->create();
    $recipient = User::factory()->create();

    $this->actingAs($user)
        ->post(route('messages.store'), array_merge([
            'recipient_id' => $recipient->id,
            'subject' => 'Hello',
            'body' => 'How are you?',
        ], $payload))
        ->assertSessionHasErrors($invalidField);

    expect(Message::count())->toBe(0);
})->with([
    'missing recipient' => [['recipient_id' => null], 'recipient_id'],
    'nonexistent recipient' => [['recipient_id' => 999999], 'recipient_id'],
    'missing subject' => [['subject' => ''], 'subject'],
    'too short subject' => [['subject' => 'Hi'], 'subject'],
    'too long subject' => [['subject' => str_repeat('a', 256)], 'subject'],
    'missing body' => [['body' => ''], 'body'],
    'too short body' => [['body' => 'Hey'], 'body'],
]);
