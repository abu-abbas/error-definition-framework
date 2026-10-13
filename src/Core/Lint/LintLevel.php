<?php

declare(strict_types=1);

namespace Edf\Core\Lint;

enum LintLevel: string
{
    case ERROR = 'error';
    case WARNING = 'warning';
}
