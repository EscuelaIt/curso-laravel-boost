<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        Project::factory(12)
            ->sequence(fn ($sequence) => ['is_active' => $sequence->index % 3 !== 0])
            ->create()
            ->each(function (Project $project) use ($users): void {
                if ($users->isEmpty()) {
                    return;
                }

                $project->users()->attach(
                    $users->random(min(3, $users->count()))->pluck('id')
                );
            });
    }
}
