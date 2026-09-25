<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->count() < 2) {
            return;
        }

        foreach (range(1, 30) as $iteration) {
            [$sender, $recipient] = $users->random(2)->all();

            Message::factory()
                ->when(fake()->boolean(), fn ($factory) => $factory->read())
                ->create([
                    'sender_id' => $sender->id,
                    'recipient_id' => $recipient->id,
                ]);
        }

        $miguel = $users->firstWhere('email', 'miguel@escuela.it');

        if ($miguel === null) {
            return;
        }

        $users->reject(fn (User $user) => $user->is($miguel))
            ->random(3)
            ->each(fn (User $sender) => Message::factory()->create([
                'sender_id' => $sender->id,
                'recipient_id' => $miguel->id,
            ]));
    }
}
