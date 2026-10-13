<?php

declare(strict_types=1);

namespace Edf\Tests\Fixtures;

use Edf\Core\Attributes\ErrorDefinition;
use Edf\Core\ErrorCategory;
use Edf\Core\ErrorCode;
use Edf\Core\ErrorSeverity;

enum SuratMasukError: string implements ErrorCode
{
    #[ErrorDefinition(
        message: 'Nomor surat wajib diisi.',
        category: ErrorCategory::VALIDATION,
        httpStatus: 422,
        severity: ErrorSeverity::LOW,
    )]
    case NOMOR_SURAT_REQUIRED = 'SM-VAL-001';

    #[ErrorDefinition(
        message: 'Draft sedang dikunci.',
        category: ErrorCategory::WORKFLOW,
        httpStatus: 409,
    )]
    case DRAFT_LOCKED = 'SM-WF-001';

    #[ErrorDefinition(
        message: 'Layanan tanda tangan elektronik tidak tersedia.',
        category: ErrorCategory::INTEGRATION,
        httpStatus: 503,
        severity: ErrorSeverity::HIGH,
        retryable: true,
    )]
    case TTE_UNAVAILABLE = 'SM-INT-001';

    #[ErrorDefinition(
        message: 'Penyimpanan surat tidak dapat diakses.',
        category: ErrorCategory::SYSTEM,
        httpStatus: 500,
        severity: ErrorSeverity::CRITICAL,
    )]
    case STORAGE_DOWN = 'SM-SYS-001';

    case UNDEFINED = 'SM-SYS-999';
}
