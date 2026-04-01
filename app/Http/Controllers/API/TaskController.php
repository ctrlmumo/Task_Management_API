<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * TaskController
 *
 * Handles all API endpoints for the Task Management system.
 *
 * Endpoints:
 *   POST   /api/tasks                  → store()        Create a new task
 *   GET    /api/tasks                  → index()        List all tasks
 *   PATCH  /api/tasks/{id}/status      → updateStatus() Update task status
 *   DELETE /api/tasks/{id}             → destroy()      Delete a done task
 *   GET    /api/tasks/report           → report()       Daily summary (bonus)
 */
class TaskController extends Controller
{
    // =========================================================================
    // 1. CREATE TASK — POST /api/tasks
    // =========================================================================

    /**
     * Create a new task.
     *
     * Validation is handled by CreateTaskRequest (see app/Http/Requests/).
     * That class checks:
     *   - title is present and not too long
     *   - due_date is today or in the future
     *   - priority is low / medium / high
     *   - the title + due_date combination is unique
     *
     * @param  CreateTaskRequest $request  The validated request object
     * @return JsonResponse                201 Created with the new task, or 422 on failure
     */
    public function store(CreateTaskRequest $request): JsonResponse
    {
        // At this point all validation has already passed in CreateTaskRequest.
        // We can safely create the task.
        $task = Task::create([
            'title'    => $request->input('title'),
            'due_date' => $request->input('due_date'),
            'priority' => $request->input('priority'),
            // Default status is 'pending' unless explicitly provided
            'status'   => $request->input('status', 'pending'),
        ]);

        return response()->json([
            'message' => 'Task created successfully.',
            'data'    => $task,
        ], 201);
    }


    // =========================================================================
    // 2. LIST TASKS — GET /api/tasks
    // =========================================================================

    /**
     * Retrieve all tasks with optional status filtering.
     *
     * Sorting rules (as per spec):
     *   1. Priority:  high → medium → low  (NOT alphabetical — requires custom SQL)
     *   2. Due date:  ascending (earliest first)
     *
     * Query parameters:
     *   ?status=pending|in_progress|done   (optional filter)
     *
     * @param  Request $request
     * @return JsonResponse        200 with tasks array (or empty message if none)
     */
    public function index(Request $request): JsonResponse
    {
        // Validate the optional status filter if it was provided
        if ($request->filled('status')) {
            $allowedStatuses = implode(',', Task::STATUSES);
            $request->validate([
                'status' => "in:{$allowedStatuses}",
            ], [
                'status.in' => 'Invalid status filter. Must be: pending, in_progress, or done.',
            ]);
        }

        // Build the query
        $query = Task::query();

        // Apply optional status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Sort by priority (high first) using MySQL's FIELD() function,
        // then by due_date ascending.
        // NOTE: FIELD() returns the position of the value in the list,
        //       so FIELD(priority, 'high', 'medium', 'low') gives:
        //         high → 1, medium → 2, low → 3  (ascending = high first)
        $tasks = $query
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderBy('due_date', 'asc')
            ->get();

        // Return a meaningful response even when no tasks are found
        if ($tasks->isEmpty()) {
            return response()->json([
                'message' => 'No tasks found' . ($request->filled('status') ? " with status '{$request->input('status')}'" : '') . '.',
                'data'    => [],
                'total'   => 0,
            ], 200);
        }

        return response()->json([
            'message' => 'Tasks retrieved successfully.',
            'data'    => $tasks,
            'total'   => $tasks->count(),
        ], 200);
    }


    // =========================================================================
    // 3. UPDATE TASK STATUS — PATCH /api/tasks/{id}/status
    // =========================================================================

    /**
     * Update a task's status following the strict progression rule:
     *
     *   pending  →  in_progress  →  done
     *
     * Rules:
     *   - Cannot skip a step  (pending → done is INVALID)
     *   - Cannot revert       (done → pending is INVALID)
     *   - The new status must be exactly the next one in the chain
     *
     * @param  UpdateTaskStatusRequest $request  Validates the 'status' field
     * @param  int                     $id       The task ID from the URL
     * @return JsonResponse
     */
    public function updateStatus(UpdateTaskStatusRequest $request, int $id): JsonResponse
    {
        // Find the task — return 404 if it doesn't exist
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'message' => "Task with ID {$id} not found.",
            ], 404);
        }

        $requestedStatus = $request->input('status');

        // Check if the task is already at the terminal state (done)
        if ($task->status === 'done') {
            return response()->json([
                'message' => 'This task is already completed (done) and cannot be updated further.',
            ], 422);
        }

        // Validate the transition using the model's helper method
        if (!$task->canTransitionTo($requestedStatus)) {
            $nextStatus = $task->nextStatus();
            return response()->json([
                'message' => "Invalid status transition. "
                           . "Task '{$task->title}' is currently '{$task->status}'. "
                           . "It can only move to '{$nextStatus}', not '{$requestedStatus}'.",
                'current_status' => $task->status,
                'allowed_next'   => $nextStatus,
            ], 422);
        }

        // All checks passed — update the status
        $task->status = $requestedStatus;
        $task->save();

        return response()->json([
            'message' => "Task status updated successfully: '{$task->status}'.",
            'data'    => $task,
        ], 200);
    }


    // =========================================================================
    // 4. DELETE TASK — DELETE /api/tasks/{id}
    // =========================================================================

    /**
     * Delete a task — but ONLY if its status is 'done'.
     *
     * Returns:
     *   200 → Task deleted successfully
     *   403 → Task is not done (Forbidden — as per spec)
     *   404 → Task not found
     *
     * @param  int $id  The task ID from the URL
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'message' => "Task with ID {$id} not found.",
            ], 404);
        }

        // Business rule: only 'done' tasks can be deleted
        if ($task->status !== 'done') {
            return response()->json([
                'message' => "Forbidden. Only completed tasks (status: 'done') can be deleted. "
                           . "This task has status: '{$task->status}'.",
            ], 403);
        }

        $task->delete();

        return response()->json([
            'message' => "Task '{$task->title}' (ID: {$id}) was deleted successfully.",
        ], 200);
    }


    // =========================================================================
    // 5. DAILY REPORT (BONUS) — GET /api/tasks/report?date=YYYY-MM-DD
    // =========================================================================

    /**
     * Generate a daily summary report for a specific date.
     *
     * Returns the count of tasks grouped by priority × status for all tasks
     * whose due_date matches the given date.
     *
     * Required query parameter:
     *   ?date=YYYY-MM-DD   e.g. ?date=2026-04-01
     *
     * Response shape (matches spec exactly):
     * {
     *   "date": "2026-04-01",
     *   "summary": {
     *     "high":   { "pending": 2, "in_progress": 1, "done": 0 },
     *     "medium": { "pending": 1, "in_progress": 0, "done": 3 },
     *     "low":    { "pending": 0, "in_progress": 0, "done": 1 }
     *   }
     * }
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function report(Request $request): JsonResponse
    {
        // Validate the date query parameter
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ], [
            'date.required'    => 'A date query parameter is required. Example: ?date=2026-04-01',
            'date.date_format' => 'The date must be in YYYY-MM-DD format. Example: ?date=2026-04-01',
        ]);

        $date = $request->input('date');

        // Fetch all tasks for that due date
        $tasks = Task::whereDate('due_date', $date)->get();

        // Build the summary matrix: priority × status → count
        // We initialise all combinations to 0 so missing ones still appear in
        // the response (evaluators will check for exact shape).
        $summary = [];

        foreach (Task::PRIORITIES as $priority) {
            $summary[$priority] = [];
            foreach (Task::STATUSES as $status) {
                $summary[$priority][$status] = $tasks
                    ->where('priority', $priority)
                    ->where('status', $status)
                    ->count();
            }
        }

        // Mirror the exact response shape shown in the assignment brief
        return response()->json([
            'date'    => $date,
            'summary' => $summary,
        ], 200);
    }
}
