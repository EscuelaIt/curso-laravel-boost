<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Negartarh\APIWrapper\Facades\APIResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->statefulApi();

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        /**
         * APIResponse throws an HttpResponseException for error statuses,
         * so the wrapped response is taken from the exception.
         */
        $renderApiError = function (string $method): Response {
            try {
                APIResponse::{$method}();
            } catch (HttpResponseException $exception) {
                return $exception->getResponse();
            }
        };

        $exceptions->render(function (AuthenticationException $exception, Request $request) use ($renderApiError) {
            if ($request->is('api/*')) {
                return $renderApiError('unauthorized');
            }
        });

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) use ($renderApiError) {
            if ($request->is('api/*')) {
                return $renderApiError('notFound');
            }
        });
    })->create();
