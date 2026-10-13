<?php

declare(strict_types=1);

namespace Edf\Tests\Fixtures\Validation;

use Edf\Core\Attributes\ErrorDefinition;
use Edf\Core\ErrorCategory;
use Edf\Core\ErrorCode;
use Edf\Core\ErrorSeverity;

enum SuratMasukValidationError: string implements ErrorCode
{
    #[ErrorDefinition(
        message: ':attribute wajib diisi.',
        category: ErrorCategory::VALIDATION,
        httpStatus: 422,
        severity: ErrorSeverity::LOW,
    )]
    case NOMOR_SURAT_REQUIRED = 'SM-VAL-001';

    #[ErrorDefinition(
        message: ':attribute minimal :min karakter.',
        category: ErrorCategory::VALIDATION,
        httpStatus: 422,
        severity: ErrorSeverity::LOW,
    )]
    case PERIHAL_TOO_SHORT = 'SM-VAL-002';

    #[ErrorDefinition(
        message: ':attribute harus berupa teks.',
        category: ErrorCategory::VALIDATION,
        httpStatus: 422,
        severity: ErrorSeverity::LOW,
    )]
    case PERIHAL_MUST_BE_STRING = 'SM-VAL-006';

    #[ErrorDefinition(
        message: ':attribute wajib diunggah.',
        category: ErrorCategory::VALIDATION,
        httpStatus: 422,
        severity: ErrorSeverity::LOW,
    )]
    case ATTACHMENT_REQUIRED = 'SM-VAL-003';

    #[ErrorDefinition(
        message: ':attribute harus huruf kapital.',
        category: ErrorCategory::VALIDATION,
        httpStatus: 422,
        severity: ErrorSeverity::LOW,
    )]
    case KODE_MUST_BE_UPPERCASE = 'SM-VAL-004';

    #[ErrorDefinition(
        message: 'Jadwal tidak tersedia.',
        category: ErrorCategory::VALIDATION,
        httpStatus: 422,
        severity: ErrorSeverity::LOW,
    )]
    case SCHEDULE_UNAVAILABLE = 'SM-VAL-005';
}
