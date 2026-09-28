<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Negartarh\APIWrapper\Facades\APIResponse;

class DeleteUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * A user cannot delete their own account.
     */
    public function authorize(): bool
    {
        return $this->user()->isNot($this->route('user'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Respond with the API wrapper format when authorization fails.
     */
    protected function failedAuthorization(): void
    {
        APIResponse::forbidden([], 'You cannot delete your own user.');
    }
}
