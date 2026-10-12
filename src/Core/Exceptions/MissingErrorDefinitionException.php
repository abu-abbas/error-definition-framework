<?php

declare(strict_types=1);

namespace Edf\Core\Exceptions;

use BackedEnum;
use LogicException;

/**
 * Enum ErrorCode tidak memiliki attribute ErrorDefinition.
 *
 * Merupakan kesalahan konfigurasi programmer, bukan kondisi bisnis normal.
 */
final class MissingErrorDefinitionException extends LogicException
{
    public static function for(BackedEnum $error): self
    {
        return new self(sprintf(
            'Error code %s::%s tidak memiliki attribute ErrorDefinition.',
            $error::class,
            $error->name,
        ));
    }
}
