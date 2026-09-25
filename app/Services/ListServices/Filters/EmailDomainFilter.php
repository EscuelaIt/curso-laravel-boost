<?php

namespace App\Services\ListServices\Filters;

use EscuelaIT\APIKit\CustomFilter;
use Illuminate\Database\Eloquent\Builder;

class EmailDomainFilter extends CustomFilter
{
    protected $filterName = 'email_domain';

    public function apply(Builder $query): void
    {
        $domain = ltrim(trim((string) $this->getFilterValue()), '@');

        if ($domain === '') {
            return;
        }

        $query->where('email', 'like', '%@'.$domain);
    }
}
