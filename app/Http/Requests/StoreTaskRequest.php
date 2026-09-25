<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:pending,completed,incomplete'],
            'priority' => ['required', 'in:low,medium,high'],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
            'category_id' => ['required', 'exists:categories,id']
        ];

    }

    public function messages(): array
    {
        return [
            'required' => 'bro The :attribute field is required.',
            'in' => 'bro The :attribute must be one of the following values: :values',
            'date' => 'dude The :attribute must be a valid date ',
            'after_or_equal' => 'dude The :attribute must be a date after or equal to today.',
            'exists' => 'bro The selected :attribute is not found.'
        ];

    }

}

