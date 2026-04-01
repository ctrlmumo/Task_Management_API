<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class CreateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'     => 'required|string|max:255',
            'due_date'  => 'required|date|after_or_equal:today',
            'priority'  => 'required|in:low,medium,high',
            'status'    => 'sometimes|in:pending,in_progress,done',
        ];
    }


    public function messages(): array
    {
        return [
            'title.required'            => 'A task title is required.',
            'title.max'                 => 'The title cannot exceed 255 characters.',
            'due_date.required'         => 'A due date is required.',
            'due_date.date'             => 'The due date must be a valid date (e.g. 2026-04-01).',
            'due_date.after_or_equal'   => 'The due date must be today or a future date.',
            'priority.required'         => 'A priority level is required.',
            'priority.in'               => 'Priority must be one of: low, medium, high.',
            'status.in'                 => 'Status must be one of: pending, in_progress, done.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {

            if ($validator->errors()->hasAny(['title', 'due_date'])) {
                return;
            }

            $exists = Task::where('title', $this->input('title'))
                          ->whereDate('due_date', $this->input('due_date'))
                          ->exists();

            if ($exists) {
                $validator->errors()->add(
                    'title',
                    'A task with this title already exists for the chosen due date. Please use a different title or due date.'
                );
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation failed. Please check your input.',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
