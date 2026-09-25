<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AssignTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('assign', $this->route('task'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_ids' => ['required', 'array', 'min:1'],
            // check every user id exists in users table
            'user_ids.*' => ['exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_ids.required' => 'Bro, the user_ids field is required.',
            'user_ids.array' => 'Bro, the user_ids must be an array.',
            'user_ids.min' => 'Bro, you must assign the task to at least one user.',
            'user_ids.*.exists' => 'Bro, one or more selected users do not exist.',
        ];
    }

}

