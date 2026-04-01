<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CreateTasksTable Migration
 *
 * This migration creates the 'tasks' table in your MySQL database.
 * Run it with:  php artisan migrate
 * Roll it back: php artisan migrate:rollback
 */
return new class extends Migration
{
    /**
     * Run the migration — creates the tasks table.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {

            // Auto-incrementing primary key (id: 1, 2, 3, ...)
            $table->id();

            // Task title — plain text, required
            $table->string('title');

            // Deadline — stored as a date (YYYY-MM-DD), no time component
            $table->date('due_date');

            // Priority level — only these three values are allowed
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');

            // Current workflow status — starts at pending, can only move forward
            $table->enum('status', ['pending', 'in_progress', 'done'])->default('pending');

            // Laravel's automatic timestamp columns: created_at and updated_at
            $table->timestamps();

            // ----------------------------------------------------------------
            // Composite unique index on (title + due_date)
            //
            // This enforces the business rule:
            //   "A task title cannot be duplicated for the same due date."
            //   However, the same title CAN exist on different due dates.
            //
            // Example:
            //   "Write report" due 2026-04-01 → ALLOWED
            //   "Write report" due 2026-04-05 → ALLOWED (different date)
            //   "Write report" due 2026-04-01 again → REJECTED (duplicate!)
            // ----------------------------------------------------------------
            $table->unique(['title', 'due_date'], 'tasks_title_due_date_unique');
        });
    }

    /**
     * Reverse the migration — drops the tasks table.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
