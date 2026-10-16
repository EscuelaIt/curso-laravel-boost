<?php

namespace App\Http\Requests\Project;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Negartarh\APIWrapper\Facades\APIResponse;

class ToggleProjectUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * The payload is the one sent by the dile-relation-checker component.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', Rule::exists(User::class, 'id')],
            'checked' => ['required', 'boolean'],
        ];
    }

    /**
     * Respond with the API wrapper format when validation fails.
     */
    protected function failedValidation(Validator $validator): void
    {
        APIResponse::unprocessableEntity($validator->errors());
    }
}
