<?php

declare(strict_types=1);

namespace Edf\Tests\Fixtures\Lint;

use Edf\Core\ErrorCode;

enum IntBackedError: int implements ErrorCode
{
    case FAILED = 1;
}
