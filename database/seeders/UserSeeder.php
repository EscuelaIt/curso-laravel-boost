<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(10)->sequence(
            fn ($sequence) => [
                'name' => 'User '.($sequence->index + 1),
                'email' => 'user'.($sequence->index + 1).'@example.com',
            ]
        )->create();

        User::factory()->create([
            'name' => 'Miguel Angel',
            'email' => 'miguel@escuela.it',
            'password' => 'password',
        ]);
    }
}
