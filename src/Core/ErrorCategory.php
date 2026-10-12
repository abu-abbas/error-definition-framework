<?php

declare(strict_types=1);

namespace Edf\Core;

enum ErrorCategory: string
{
    case VALIDATION = 'validation';
    case AUTHENTICATION = 'authentication';
    case AUTHORIZATION = 'authorization';
    case NOT_FOUND = 'not_found';
    case BUSINESS_RULE = 'business_rule';
    case WORKFLOW = 'workflow';
    case INTEGRATION = 'integration';
    case SYSTEM = 'system';
}
