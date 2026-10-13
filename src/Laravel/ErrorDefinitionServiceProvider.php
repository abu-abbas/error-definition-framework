<?php

declare(strict_types=1);

namespace Edf\Laravel;

use Edf\Core\ErrorDefinitionReader;
use Edf\Core\Exceptions\ApplicationException;
use Edf\Core\Logging\ApplicationExceptionReporter;
use Edf\Core\Logging\ContextSanitizer;
use Edf\Laravel\Console\GenerateCommand;
use Edf\Laravel\Console\LintCommand;
use Edf\Laravel\Discovery\ErrorDefinitionDiscovery;
use Edf\Laravel\Validation\ErrorValidationException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use ReflectionObject;
use Throwable;
use WeakMap;

final class ErrorDefinitionServiceProvider extends ServiceProvider
{
    /** @var WeakMap<Handler, true> handler yang sudah diberi callback, agar tidak terdaftar ganda */
    private WeakMap $configuredHandlers;

    public function register(): void
    {
        $this->configuredHandlers = new WeakMap();

        // Singleton agar cache Reader digunakan ulang selama lifecycle process (Decision 1.3.1).
        $this->app->singleton(ErrorDefinitionReader::class);
        $this->app->singleton(ErrorResponseRenderer::class);

        $this->mergeConfigFrom(__DIR__.'/../../config/error-definition.php', 'error-definition');

        $this->app->singleton(ContextSanitizer::class, fn ($app) => new ContextSanitizer(
            $app['config']->get('error-definition.logging.additional_sensitive_keys', []),
        ));
        $this->app->singleton(ApplicationExceptionReporter::class);

        // Snapshot discovery hanya dibuat ketika command atau tooling memintanya (Decision 1.9).
        $this->app->singleton(ErrorDefinitionDiscovery::class, fn ($app) => new ErrorDefinitionDiscovery($app->basePath()));
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/error-definition.php' => $this->app->configPath('error-definition.php'),
        ], 'error-definition-config');

        if ($this->app->runningInConsole()) {
            $this->commands([LintCommand::class, GenerateCommand::class]);
        }

        $this->callAfterResolving(ExceptionHandler::class, function (ExceptionHandler $resolved): void {
            $handler = $this->foundationHandler($resolved);

            if ($handler === null || isset($this->configuredHandlers[$handler])) {
                return;
            }

            $this->configuredHandlers[$handler] = true;

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
     * Handler aplikasi dapat dibungkus decorator, misalnya adapter Collision
     * pada artisan command dan queue worker. Decorator tersebut meneruskan
     * report/render ke handler asli, jadi callback dipasang pada handler asli.
     */
    private function foundationHandler(ExceptionHandler $handler): ?Handler
    {
        if ($handler instanceof Handler) {
            return $handler;
        }

        foreach ((new ReflectionObject($handler))->getProperties() as $property) {
            if ($property->isStatic() || ! $property->isInitialized($handler)) {
                continue;
            }

            $value = $property->getValue($handler);

            if ($value instanceof Handler) {
                return $value;
            }
        }

        return null;
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
