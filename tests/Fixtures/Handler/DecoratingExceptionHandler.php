<?php

declare(strict_types=1);

namespace Edf\Tests\Fixtures\Handler;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Throwable;

/**
 * Meniru adapter Collision: membungkus handler aplikasi dan meneruskan setiap call.
 */
final class DecoratingExceptionHandler implements ExceptionHandler
{
    public function __construct(
        private readonly ExceptionHandler $appExceptionHandler,
    ) {}

    public function report(Throwable $e)
    {
        $this->appExceptionHandler->report($e);
    }

    public function shouldReport(Throwable $e)
    {
        return $this->appExceptionHandler->shouldReport($e);
    }

    public function render($request, Throwable $e)
    {
        return $this->appExceptionHandler->render($request, $e);
    }

    public function renderForConsole($output, Throwable $e)
    {
        $this->appExceptionHandler->renderForConsole($output, $e);
    }
}
