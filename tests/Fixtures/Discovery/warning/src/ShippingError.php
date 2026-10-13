<?php

declare(strict_types=1);

namespace EdfDiscoveryWarning;

use Edf\Core\Attributes\ErrorDefinition;
use Edf\Core\ErrorCategory;
use Edf\Core\ErrorCode;

enum ShippingError: string implements ErrorCode
{
    #[ErrorDefinition(message: ':attribute wajib diisi.', category: ErrorCategory::VALIDATION, httpStatus: 422)]
    case ADDRESS_REQUIRED = 'SHP-VAL-001';
}
