<?php

namespace App\Http\Requests\Project;

use App\Concerns\ProjectValidationRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Negartarh\APIWrapper\Facades\APIResponse;

class StoreProjectRequest extends FormRequest
{
    use ProjectValidationRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->projectRules();
    }

    /**
     * Respond with the API wrapper format when validation fails.
     */
    protected function failedValidation(Validator $validator): void
    {
        APIResponse::unprocessableEntity($validator->errors());
    }
}
