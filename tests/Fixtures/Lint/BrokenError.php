<?php

declare(strict_types=1);

namespace Edf\Tests\Fixtures\Lint;

use Edf\Core\Attributes\ErrorDefinition;
use Edf\Core\ErrorCategory;
use Edf\Core\ErrorCode;

enum BrokenError: string implements ErrorCode
{
    #[ErrorDefinition(message: 'Valid.', category: ErrorCategory::WORKFLOW, httpStatus: 409)]
    case VALID = 'LN-WF-001';

    case MISSING_DEFINITION = 'LN-WF-002';

    #[ErrorDefinition(message: 'Pertama.', category: ErrorCategory::WORKFLOW, httpStatus: 409)]
    #[ErrorDefinition(message: 'Kedua.', category: ErrorCategory::WORKFLOW, httpStatus: 409)]
    case DOUBLE_DEFINITION = 'LN-WF-003';

    #[ErrorDefinition(message: 'Format salah.', category: ErrorCategory::WORKFLOW, httpStatus: 409)]
    case BAD_CODE = 'draft_locked';

    #[ErrorDefinition(message: '   ', category: ErrorCategory::WORKFLOW, httpStatus: 409)]
    case EMPTY_MESSAGE = 'LN-WF-004';

    #[ErrorDefinition(message: 'Redirect.', category: ErrorCategory::WORKFLOW, httpStatus: 302)]
    case BAD_STATUS = 'LN-WF-005';

    #[ErrorDefinition(message: 'Kode milik SuratMasukError.', category: ErrorCategory::WORKFLOW, httpStatus: 409)]
    case DUPLICATE_CODE = 'SM-WF-001';
}
