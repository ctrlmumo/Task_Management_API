<?php

use App\Http\Controllers\API\TaskController;
use Illuminate\Support\Facades\Route;

// generate reports
Route::get('/tasks/report', [TaskController::class, 'report']);
// list all tasks
Route::get('/tasks', [TaskController::class, 'index']);
// create a new task
Route::post('/tasks', [TaskController::class, 'store']);
// update a task's status
Route::patch('/tasks/{id}/status', [TaskController::class, 'updateStatus']);
// delete a task
Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);