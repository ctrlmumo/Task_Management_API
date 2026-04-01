<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

/**
 * TaskSeeder
 *
 * Populates the tasks table with demo data so evaluators can test
 * all endpoints immediately without creating tasks manually first.
 *
 * Run with:  php artisan db:seed
 *        or: php artisan migrate:fresh --seed  (fresh wipe + migrate + seed)
 */
class TaskSeeder extends Seeder
{
    public function run(): void
    {
        // Clear any existing tasks before seeding
        Task::truncate();

        $tasks = [
            // ----------------------------------------------------------------
            // High priority tasks — appear first in list results
            // ----------------------------------------------------------------
            [
                'title'    => 'Fix critical authentication bug',
                'due_date' => now()->addDays(1)->format('Y-m-d'),
                'priority' => 'high',
                'status'   => 'in_progress',
            ],
            [
                'title'    => 'Deploy hotfix to production',
                'due_date' => now()->addDays(2)->format('Y-m-d'),
                'priority' => 'high',
                'status'   => 'pending',
            ],
            [
                'title'    => 'Security audit review',
                'due_date' => now()->addDays(3)->format('Y-m-d'),
                'priority' => 'high',
                'status'   => 'pending',
            ],
            [
                'title'    => 'Database performance optimisation',
                'due_date' => now()->addDays(1)->format('Y-m-d'),
                'priority' => 'high',
                'status'   => 'done',  // This one can be deleted — useful for testing
            ],

            // ----------------------------------------------------------------
            // Medium priority tasks — appear after high in list results
            // ----------------------------------------------------------------
            [
                'title'    => 'Update API documentation',
                'due_date' => now()->addDays(4)->format('Y-m-d'),
                'priority' => 'medium',
                'status'   => 'pending',
            ],
            [
                'title'    => 'Code review for PR #47',
                'due_date' => now()->addDays(2)->format('Y-m-d'),
                'priority' => 'medium',
                'status'   => 'in_progress',
            ],
            [
                'title'    => 'Write unit tests for TaskController',
                'due_date' => now()->addDays(5)->format('Y-m-d'),
                'priority' => 'medium',
                'status'   => 'pending',
            ],
            [
                'title'    => 'Refactor user service module',
                'due_date' => now()->addDays(3)->format('Y-m-d'),
                'priority' => 'medium',
                'status'   => 'done',  // Can be deleted — useful for testing
            ],

            // ----------------------------------------------------------------
            // Low priority tasks — appear last in list results
            // ----------------------------------------------------------------
            [
                'title'    => 'Update README with deployment steps',
                'due_date' => now()->addDays(7)->format('Y-m-d'),
                'priority' => 'low',
                'status'   => 'pending',
            ],
            [
                'title'    => 'Clean up old feature branches',
                'due_date' => now()->addDays(10)->format('Y-m-d'),
                'priority' => 'low',
                'status'   => 'pending',
            ],
            [
                'title'    => 'Upgrade NPM dependencies',
                'due_date' => now()->addDays(6)->format('Y-m-d'),
                'priority' => 'low',
                'status'   => 'in_progress',
            ],
        ];

        foreach ($tasks as $taskData) {
            Task::create($taskData);
        }

        $this->command->info('✅  TaskSeeder: ' . count($tasks) . ' tasks created successfully.');
        $this->command->info('💡  Tip: Tasks with status "done" can be deleted via DELETE /api/tasks/{id}');
    }
}
