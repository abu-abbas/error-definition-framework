<?php

declare(strict_types=1);

namespace Edf\Laravel;

use Edf\Core\ErrorDefinitionReader;
use Edf\Core\Exceptions\ApplicationException;
use Edf\Core\Logging\ApplicationExceptionReporter;
use Edf\Core\Logging\ContextSanitizer;
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

        $this->mergeConfigFrom(__DIR__.'/../../config/error-definition.php', 'error-definition');

        $this->app->singleton(ContextSanitizer::class, fn ($app) => new ContextSanitizer(
            $app['config']->get('error-definition.logging.additional_sensitive_keys', []),
        ));
        $this->app->singleton(ApplicationExceptionReporter::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/error-definition.php' => $this->app->configPath('error-definition.php'),
        ], 'error-definition-config');

        $this->callAfterResolving(ExceptionHandler::class, function (ExceptionHandler $handler): void {
            if (! $handler instanceof Handler) {
                return;
            }

            // Mencatat tepat satu kali dan menghentikan default reporting Laravel (Decision 1.7).
            $handler->reportable(function (ApplicationException $e): void {
                $this->app->make(ApplicationExceptionReporter::class)->report($e);
            })->stop();

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
