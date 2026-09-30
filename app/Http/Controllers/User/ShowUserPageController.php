<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class ShowUserPageController extends Controller
{
    /**
     * Show the panel page with the detail of a user.
     *
     * Route model binding responds with a 404 when the user does not exist.
     * The user data is loaded from the API by the Lit component, so only its id is passed.
     */
    public function __invoke(User $user): Response
    {
        return Inertia::render('users/Show', [
            'userId' => $user->id,
        ]);
    }
}
