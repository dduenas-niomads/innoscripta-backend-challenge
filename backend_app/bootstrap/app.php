<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;
use \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Expose error to client is a security hole so here I have
        // a custom 404 error handler for the Article management API.
        // To this example, I only do it for NotFoundHttpException...
        // Rest of exceptions I will do it later.
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/articles/*')) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Article not found.'
                ], 404);
            }
        });
    })
    ->withSchedule(function (Schedule $schedule) {
        // Sync from New York Times every 5 minutes.
        $schedule->command('app:article-sync-new-york-times')->hourly();
        $schedule->command('app:article-sync-news-api-dot-org')->hourly();
        $schedule->command('app:article-sync-news-api-ai')->hourly();
    })
    ->create();
