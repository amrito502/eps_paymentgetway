<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Add to a group (like 'web')
     

        // Or register an alias if you want to use it individually by name
        // $middleware->alias('track.activity', \App\Http\Middleware\TrackUserActivity::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
