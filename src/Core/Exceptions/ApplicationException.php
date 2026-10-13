<?php

declare(strict_types=1);

namespace Edf\Core\Exceptions;

use Edf\Core\ResolvedErrorDefinition;
use RuntimeException;
use Throwable;

/**
 * Exception standar aplikasi yang hanya berfungsi sebagai data carrier.
 *
 * Message selalu berasal dari definition dan tidak dapat di-override.
 * Exception::$code tetap 0; string error code tersedia melalui definition.
 * Context tidak otomatis dikirim ke client dan tidak disanitasi di sini;
 * sanitasi dilakukan pada logging boundary.
 *
 * @see docs/decisions/1-error-definition-framework/1.4-application-exception.md
 */
final class ApplicationException extends RuntimeException
{
    /**
     * @param array<string, mixed> $context
     */
    public function __construct(
        public readonly ResolvedErrorDefinition $definition,
        public readonly array $context = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            message: $definition->message,
            code: 0,
            previous: $previous,
        );
    }
}
