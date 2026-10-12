<?php

declare(strict_types=1);

namespace Edf\Core\Attributes;

use Attribute;
use Edf\Core\ErrorCategory;
use Edf\Core\ErrorSeverity;

/**
 * Metadata runtime untuk satu enum case error.
 *
 * @see docs/decisions/1-error-definition-framework/1.1-error-definition.md
 */
#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
final readonly class ErrorDefinition
{
    public function __construct(
        public string $message,
        public ErrorCategory $category,
        public int $httpStatus,
        public ErrorSeverity $severity = ErrorSeverity::MEDIUM,
        public bool $retryable = false,
    ) {}
}
