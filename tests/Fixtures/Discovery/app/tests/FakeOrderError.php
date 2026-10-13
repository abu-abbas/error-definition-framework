<?php

declare(strict_types=1);

namespace EdfDiscoveryApp\Tests;

use Edf\Core\ErrorCode;

enum FakeOrderError: string implements ErrorCode
{
    case FAKE = 'ORD-VAL-001';
}
