<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Show the dashboard of the authenticated user.
     */
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Dashboard', [
            'unreadMessagesCount' => Inertia::defer(
                fn (): int => $request->user()->receivedMessages()->unread()->count()
            ),
        ]);
    }
}
