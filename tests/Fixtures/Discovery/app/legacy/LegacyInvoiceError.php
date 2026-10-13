<?php

declare(strict_types=1);

use Edf\Core\Attributes\ErrorDefinition;
use Edf\Core\ErrorCategory;
use Edf\Core\ErrorCode;

enum LegacyInvoiceError: string implements ErrorCode
{
    #[ErrorDefinition(message: 'Invoice sudah dibayar.', category: ErrorCategory::BUSINESS_RULE, httpStatus: 409)]
    case ALREADY_PAID = 'INV-BR-001';
}
