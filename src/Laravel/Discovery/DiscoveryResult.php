<?php

declare(strict_types=1);

namespace Edf\Laravel\Discovery;

use Edf\Core\ErrorCode;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Target aplikasi untuk linter dan generator: FQCN unik dan terurut ascending.
 */
final readonly class DiscoveryResult
{
    /**
     * @param list<class-string<ErrorCode>> $errorEnums
     * @param list<class-string<FormRequest>> $formRequests
     */
    public function __construct(
        public array $errorEnums,
        public array $formRequests,
    ) {}
}
