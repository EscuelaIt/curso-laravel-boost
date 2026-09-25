<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\ListServices\UserListService;
use EscuelaIT\APIKit\ResourceListable;
use Illuminate\Http\Response;

class ListUserController extends Controller
{
    use ResourceListable;

    public function __construct(
        private UserListService $userListService
    ) {}

    public function index(): Response
    {
        return $this->list($this->userListService);
    }
}
