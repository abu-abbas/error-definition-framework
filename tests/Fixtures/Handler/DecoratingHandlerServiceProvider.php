<?php

declare(strict_types=1);

namespace Edf\Tests\Fixtures\Handler;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;

/**
 * Seperti CollisionServiceProvider: resolve handler saat register, lalu bind ulang ke decorator.
 */
final class DecoratingHandlerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $appExceptionHandler = $this->app->make(ExceptionHandler::class);

        $this->app->singleton(
            ExceptionHandler::class,
            fn () => new DecoratingExceptionHandler($appExceptionHandler),
        );
    }
}
