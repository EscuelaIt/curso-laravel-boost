<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Services\ListServices\ProjectListService;
use EscuelaIT\APIKit\ResourceListable;
use Illuminate\Http\Response;

class ListProjectController extends Controller
{
    use ResourceListable;

    public function __construct(
        private ProjectListService $projectListService
    ) {}

    public function index(): Response
    {
        return $this->list($this->projectListService);
    }
}
