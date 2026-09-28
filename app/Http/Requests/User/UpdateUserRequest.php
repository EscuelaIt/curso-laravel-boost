<?php

namespace App\Http\Requests\User;

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Negartarh\APIWrapper\Facades\APIResponse;

class UpdateUserRequest extends FormRequest
{
    use ProfileValidationRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * The password is optional: when it is left empty the current one is kept.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            ...$this->profileRules($this->route('user')->id),
            'password' => ['exclude_if:password,null', 'nullable', 'string', Password::default(), 'confirmed'],
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
