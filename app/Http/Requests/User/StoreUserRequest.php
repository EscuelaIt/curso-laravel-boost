<?php

namespace App\Http\Requests\User;

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Negartarh\APIWrapper\Facades\APIResponse;

class StoreUserRequest extends FormRequest
{
    use ProfileValidationRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            ...$this->profileRules(),
            'is_favorite' => ['sometimes', 'boolean'],
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
