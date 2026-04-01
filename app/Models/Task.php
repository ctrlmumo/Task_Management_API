<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Task Model
 *
 * Represents a single task in the task management system.
 *
 * @property int         $id
 * @property string      $title
 * @property string      $due_date
 * @property string      $priority   low | medium | high
 * @property string      $status     pending | in_progress | done
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Task extends Model
{
    use HasFactory;

    // -------------------------------------------------------------------------
    // Fillable columns (mass-assignment protection)
    // -------------------------------------------------------------------------
    protected $fillable = [
        'title',
        'due_date',
        'priority',
        'status',
    ];

    // -------------------------------------------------------------------------
    // Type casting — ensures due_date is always treated as a date object
    // -------------------------------------------------------------------------
    protected $casts = [
        'due_date' => 'date:Y-m-d',
    ];

    // -------------------------------------------------------------------------
    // Constants — single source of truth for allowed values
    // -------------------------------------------------------------------------

    /** Allowed values for the priority column */
    const PRIORITIES = ['low', 'medium', 'high'];

    /** Allowed values for the status column */
    const STATUSES = ['pending', 'in_progress', 'done'];

    /**
     * Status transition map.
     *
     * Defines what the NEXT valid status is for each current status.
     * A null value means the status is terminal (cannot progress further).
     *
     * pending → in_progress → done → (terminal)
     */
    const STATUS_TRANSITIONS = [
        'pending'     => 'in_progress',
        'in_progress' => 'done',
        'done'        => null,
    ];

    // -------------------------------------------------------------------------
    // Helper Methods
    // -------------------------------------------------------------------------

    /**
     * Returns the next valid status for this task, or null if already done.
     *
     * Example usage:
     *   $task->nextStatus(); // returns 'in_progress' if task is 'pending'
     */
    public function nextStatus(): ?string
    {
        return self::STATUS_TRANSITIONS[$this->status];
    }

    /**
     * Checks whether a given status transition FROM this task's current
     * status TO $newStatus is allowed.
     */
    public function canTransitionTo(string $newStatus): bool
    {
        return $this->nextStatus() === $newStatus;
    }
}
