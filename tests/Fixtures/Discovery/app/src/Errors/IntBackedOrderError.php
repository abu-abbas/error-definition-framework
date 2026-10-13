<?php

declare(strict_types=1);

namespace EdfDiscoveryApp\Errors;

use Edf\Core\ErrorCode;

enum IntBackedOrderError: int implements ErrorCode
{
    case FAILED = 1;
}
