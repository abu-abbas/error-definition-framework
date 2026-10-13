<?php

declare(strict_types=1);

namespace Edf\Laravel;

use Edf\Core\ErrorDefinitionReader;
use Edf\Core\Exceptions\ApplicationException;
use Edf\Laravel\Validation\ErrorValidationException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Throwable;

final class ErrorDefinitionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Singleton agar cache Reader digunakan ulang selama lifecycle process (Decision 1.3.1).
        $this->app->singleton(ErrorDefinitionReader::class);
        $this->app->singleton(ErrorResponseRenderer::class);
    }

    public function boot(): void
    {
        $this->callAfterResolving(ExceptionHandler::class, function (ExceptionHandler $handler): void {
            if (! $handler instanceof Handler) {
                return;
            }

            $handler->renderable(function (ApplicationException $e, Request $request) use ($handler) {
                return $this->shouldReturnJson($handler, $request, $e)
                    ? $this->app->make(ErrorResponseRenderer::class)->application($e)
                    : null;
            });

            $handler->renderable(function (ErrorValidationException $e, Request $request) use ($handler) {
                return $this->shouldReturnJson($handler, $request, $e)
                    ? $this->app->make(ErrorResponseRenderer::class)->validation($e)
                    : null;
            });
        });
    }

    /**
     * Mengikuti deteksi JSON milik exception handler Laravel, termasuk
     * kustomisasi shouldRenderJsonWhen() dari aplikasi.
     */
    private function shouldReturnJson(Handler $handler, Request $request, Throwable $e): bool
    {
        return (fn (): bool => (bool) $this->shouldReturnJson($request, $e))->call($handler);
    }
}
