<?php

declare(strict_types=1);

namespace Edf\Laravel\Validation;

use Edf\Core\ResolvedErrorDefinition;

/**
 * Satu validation failure yang telah dipetakan ke Error Definition.
 *
 * Property message berisi message akhir setelah placeholder Laravel diproses.
 *
 * @see docs/reference/validation-integration.md
 */
final readonly class ResolvedValidationError
{
    public function __construct(
        public string $attribute,
        public string $rule,
        public ResolvedErrorDefinition $definition,
        public string $message,
    ) {}
}
