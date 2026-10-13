<?php

declare(strict_types=1);

namespace Edf\Tests\Fixtures\Logging;

use Psr\Log\AbstractLogger;
use Stringable;

final class CollectingLogger extends AbstractLogger
{
    /** @var list<array{level: mixed, message: string, context: array<string, mixed>}> */
    public array $records = [];

    public function log($level, string|Stringable $message, array $context = []): void
    {
        $this->records[] = ['level' => $level, 'message' => (string) $message, 'context' => $context];
    }
}
