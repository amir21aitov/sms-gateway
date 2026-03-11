<?php

use App\Enums\HttpCode;
use App\Helpers\ResponseHelper;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'api_key' => \App\Http\Middleware\AuthenticateApiKey::class,
            'admin_token' => \App\Http\Middleware\AuthenticateAdminToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return ResponseHelper::response(
                    ['message' => 'Endpoint not found.'],
                    HttpCode::NOT_FOUND,
                );
            }
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return ResponseHelper::response(
                    ['message' => 'Method not allowed.'],
                    HttpCode::METHOD_NOT_ALLOWED,
                );
            }
        });
    })->create();
