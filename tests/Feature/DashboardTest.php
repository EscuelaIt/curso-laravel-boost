<?php

use App\Models\Message;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('the dashboard defers the count of unread messages received by the user', function () {
    $user = User::factory()->create();

    Message::factory()->count(2)->create(['recipient_id' => $user->id]);
    Message::factory()->read()->create(['recipient_id' => $user->id]);
    Message::factory()->create(['sender_id' => $user->id]);
    Message::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->missing('unreadMessagesCount')
            ->loadDeferredProps(fn (Assert $reload) => $reload
                ->where('unreadMessagesCount', 2)
            )
        );
});
