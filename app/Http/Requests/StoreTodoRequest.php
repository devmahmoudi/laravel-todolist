<?php

namespace App\Http\Requests;

use App\Models\Group;
use App\Models\Todo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTodoRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['string', 'nullable'],
            'group_id' => ['required', Rule::exists(Group::class, 'id')],
            'parent_id' => ['nullable', Rule::exists(Todo::class, 'id')],
            'completed_at' => ['nullable', 'date']
        ];
    }
}
