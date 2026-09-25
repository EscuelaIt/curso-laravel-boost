<?php

namespace App\Services\ListServices;

use App\Models\User;
use App\Services\ListServices\Filters\EmailDomainFilter;
use EscuelaIT\APIKit\ListService;

class UserListService extends ListService
{
    protected string $listModel = User::class;

    protected ?int $maxPerPage = 100;

    protected ?array $availableFilterColumns = ['is_favorite'];

    protected ?array $availableScopes = [];

    protected ?array $availableIncludes = [];

    protected function applyKeywordFilter(?string $keyword): void
    {
        $this->query->similar($keyword);
    }

    protected function customFilters(): array
    {
        return [new EmailDomainFilter];
    }
}
