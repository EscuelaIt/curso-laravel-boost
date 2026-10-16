<?php

namespace App\Services\ListServices;

use App\Models\Project;
use EscuelaIT\APIKit\ListService;

class ProjectListService extends ListService
{
    protected string $listModel = Project::class;

    protected ?int $maxPerPage = 100;

    protected ?array $availableFilterColumns = ['is_active'];

    protected ?array $availableScopes = [];

    protected ?array $availableIncludes = [];

    /**
     * Columns the listing can be sorted by.
     *
     * @var list<string>
     */
    protected array $availableSortFields = ['name', 'start_date'];

    /**
     * Discard any sort field or direction the listing does not support.
     *
     * @param  array<string, mixed>  $config
     */
    public function setSearchConfiguration(array $config): ListService
    {
        if (! in_array($config['sortField'] ?? null, $this->availableSortFields, true)) {
            $config['sortField'] = null;
        }

        if (! in_array($config['sortDirection'] ?? null, ['asc', 'desc'], true)) {
            $config['sortDirection'] = null;
        }

        return parent::setSearchConfiguration($config);
    }

    protected function applyKeywordFilter(?string $keyword): void
    {
        $this->query->similar($keyword);
    }
}
