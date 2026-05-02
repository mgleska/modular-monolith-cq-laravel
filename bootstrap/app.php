<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\MultipleRecordsFoundException;
use Illuminate\Database\RecordNotFoundException;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Validation\ValidationException;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Module\Offer\Access\Console\ImportOffersCon;
use Module\Product\Access\Console\ImportProductsCon;
use Module\Product\Access\Console\ImportProductsQuantityCon;
use Module\Shared\Middleware\ForceJsonResponse;
use Module\Store\Access\Console\ImportStoresCon;

if (PHP_SAPI === 'cli') {
    $commands = [
        ImportOffersCon::class,
        ImportProductsCon::class,
        ImportProductsQuantityCon::class,
        ImportStoresCon::class,
    ];
}
else {
    $commands = [];
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->remove([
            ConvertEmptyStringsToNull::class,
        ]);
        $middleware->group('api', [
            ForceJsonResponse::class,
        ]);
        $middleware->web(remove: [
            StartSession::class,
            ShareErrorsFromSession::class,
        ]);
    })
    ->withCommands($commands)
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function () {
            return true;
        });
        $exceptions->stopIgnoring([
            ModelNotFoundException::class,
            MultipleRecordsFoundException::class,
            RecordNotFoundException::class,
            RecordsNotFoundException::class,
            ValidationException::class,
        ]);
    })->create();
