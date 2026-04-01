<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * UpdateTaskStatusRequest
 *
 * Handles validation for the PATCH /api/tasks/{id}/status endpoint.
 * Only validates that the incoming 'status' field is a recognised value.
 * The actual transition logic (pending → in_progress → done) is enforced
 * in the controller because it depends on the current task state.
 */
class UpdateTaskStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,in_progress,done',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'A status value is required.',
            'status.in'       => 'Status must be one of: pending, in_progress, done.',
        ];
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
