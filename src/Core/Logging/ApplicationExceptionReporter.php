<?php

declare(strict_types=1);

namespace Edf\Core\Logging;

use Edf\Core\ErrorSeverity;
use Edf\Core\Exceptions\ApplicationException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Mencatat satu structured log event untuk setiap ApplicationException.
 *
 * Log level mengikuti severity, metadata framework berada di root context,
 * dan runtime context caller selalu disanitasi di dalam key `context`.
 *
 * @see docs/reference/logging-integration.md
 */
final class ApplicationExceptionReporter
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ContextSanitizer $sanitizer,
    ) {}

    public function report(ApplicationException $exception): void
    {
        $definition = $exception->definition;

        $this->logger->log(self::levelFor($definition->severity), $definition->message, [
            'error_code' => $definition->code,
            'category' => $definition->category->value,
            'severity' => $definition->severity->value,
            'retryable' => $definition->retryable,
            'http_status' => $definition->httpStatus,
            'context' => $this->sanitizer->sanitize($exception->context),
            'exception' => $exception,
        ]);
    }

    private static function levelFor(ErrorSeverity $severity): string
    {
        return match ($severity) {
            ErrorSeverity::LOW => LogLevel::INFO,
            ErrorSeverity::MEDIUM => LogLevel::WARNING,
            ErrorSeverity::HIGH => LogLevel::ERROR,
            ErrorSeverity::CRITICAL => LogLevel::CRITICAL,
        };
    }
}
