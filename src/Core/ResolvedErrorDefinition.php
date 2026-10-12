<?php

declare(strict_types=1);

namespace Edf\Core;

/**
 * Hasil penggabungan error code dari enum dengan metadata ErrorDefinition.
 *
 * Tidak menyimpan enum source maupun object Reflection.
 *
 * @see docs/reference/resolved-error-definition.md
 */
final readonly class ResolvedErrorDefinition
{
    public function __construct(
        public string $code,
        public string $message,
        public ErrorCategory $category,
        public int $httpStatus,
        public ErrorSeverity $severity,
        public bool $retryable,
    ) {}
}
