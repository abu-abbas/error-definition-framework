<?php

declare(strict_types=1);

namespace Edf\Tests\Unit;

use DateTimeImmutable;
use Edf\Core\ErrorSeverity;
use Edf\Core\Logging\ContextSanitizer;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ContextSanitizerTest extends TestCase
{
    public function test_it_redacts_default_keys_recursively_with_normalized_names(): void
    {
        $sanitized = (new ContextSanitizer())->sanitize([
            'user_id' => 42,
            'accessToken' => 'abc',
            'ACCESS-TOKEN' => 'abc',
            'token_count' => 3,
            'headers' => ['Authorization' => 'Bearer x', 'accept' => 'json'],
            'credentials' => ['password' => ['nested' => 'array']],
        ]);

        $this->assertSame([
            'user_id' => 42,
            'accessToken' => '[REDACTED]',
            'ACCESS-TOKEN' => '[REDACTED]',
            'token_count' => 3,
            'headers' => ['Authorization' => '[REDACTED]', 'accept' => 'json'],
            'credentials' => ['password' => '[REDACTED]'],
        ], $sanitized);
    }

    public function test_additional_keys_extend_the_baseline(): void
    {
        $sanitized = (new ContextSanitizer(['nik', 'bank_account']))->sanitize([
            'employee' => ['id' => 42, 'nik' => '3273', 'bankAccount' => '123'],
            'password' => 'rahasia',
        ]);

        $this->assertSame([
            'employee' => ['id' => 42, 'nik' => '[REDACTED]', 'bankAccount' => '[REDACTED]'],
            'password' => '[REDACTED]',
        ], $sanitized);
    }

    public function test_it_normalizes_values(): void
    {
        $resource = fopen('php://memory', 'r');

        $sanitized = (new ContextSanitizer())->sanitize([
            'null' => null,
            'flag' => false,
            'ratio' => 1.5,
            'severity' => ErrorSeverity::HIGH,
            'at' => new DateTimeImmutable('2026-10-13T12:00:00+07:00'),
            'model' => new stdClass(),
            'stream' => $resource,
            'list' => [1, 'dua'],
        ]);

        fclose($resource);

        $this->assertSame([
            'null' => null,
            'flag' => false,
            'ratio' => 1.5,
            'severity' => 'high',
            'at' => '2026-10-13T12:00:00+07:00',
            'model' => '[object stdClass]',
            'stream' => '[resource stream]',
            'list' => [1, 'dua'],
        ], $sanitized);
    }
}
