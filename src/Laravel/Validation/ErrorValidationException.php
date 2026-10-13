<?php

declare(strict_types=1);

namespace Edf\Laravel\Validation;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

/**
 * ValidationException yang membawa structured validation errors.
 *
 * Tetap merupakan ValidationException sehingga redirect, named error bag,
 * dan JSON detection milik Laravel tetap berlaku.
 *
 * @see docs/decisions/1-error-definition-framework/1.5-validation-error-mapping.md
 */
final class ErrorValidationException extends ValidationException
{
    /**
     * @param list<ResolvedValidationError> $resolvedErrors
     */
    public function __construct(
        Validator $validator,
        public readonly array $resolvedErrors,
    ) {
        parent::__construct($validator);
    }
}
