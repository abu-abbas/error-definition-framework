<?php

declare(strict_types=1);

namespace Edf\Core;

enum ErrorSeverity: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case CRITICAL = 'critical';
}
