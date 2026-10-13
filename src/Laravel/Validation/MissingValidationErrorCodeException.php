<?php

declare(strict_types=1);

namespace Edf\Laravel\Validation;

use LogicException;

/**
 * Rule yang gagal tidak memiliki mapping pada errorCodes().
 */
final class MissingValidationErrorCodeException extends LogicException
{
    public static function for(string $request, string $attribute, string $rule): self
    {
        return new self(sprintf(
            '%s tidak memiliki mapping errorCodes() untuk "%s.%s".',
            $request,
            $attribute,
            $rule,
        ));
    }
}
