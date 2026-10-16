<?php

namespace App\Concerns;

use Illuminate\Contracts\Validation\ValidationRule;

trait ProjectValidationRules
{
    /**
     * Get the validation rules used to create and update projects.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function projectRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
