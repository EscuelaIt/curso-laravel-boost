<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MessageController extends Controller
{
    /**
     * Show the list of users that can receive a message.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('messages/Index', [
            'users' => User::query()
                ->whereKeyNot($request->user()->id)
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
        ]);
    }

    /**
     * Store a message sent by the authenticated user.
     */
    public function store(StoreMessageRequest $request): RedirectResponse
    {
        $request->user()->sentMessages()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Message sent.')]);

        return to_route('messages.index');
    }
}
