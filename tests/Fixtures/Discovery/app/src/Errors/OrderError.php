<?php

declare(strict_types=1);

namespace EdfDiscoveryApp\Errors;

use Edf\Core\Attributes\ErrorDefinition;
use Edf\Core\ErrorCategory;
use Edf\Core\ErrorCode;

enum OrderError: string implements ErrorCode
{
    #[ErrorDefinition(message: ':attribute wajib diisi.', category: ErrorCategory::VALIDATION, httpStatus: 422)]
    case CUSTOMER_REQUIRED = 'ORD-VAL-001';
}
