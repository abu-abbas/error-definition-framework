<?php

declare(strict_types=1);

namespace Edf\Laravel\Validation;

use Edf\Core\ResolvedErrorDefinition;
use LogicException;

/**
 * Error Definition untuk validation harus berkategori VALIDATION, berstatus 422,
 * dan tidak retryable.
 */
final class InvalidValidationErrorDefinitionException extends LogicException
{
    public static function for(ResolvedErrorDefinition $definition): self
    {
        return new self(sprintf(
            'Error code %s tidak dapat digunakan untuk validation: wajib category VALIDATION, httpStatus 422, dan retryable false.',
            $definition->code,
        ));
    }
}
