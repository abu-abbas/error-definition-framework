<?php

declare(strict_types=1);

namespace Edf\Tests\Unit;

use Edf\Core\ErrorDefinitionReader;
use Edf\Core\Exceptions\ApplicationException;
use Edf\Core\Logging\ApplicationExceptionReporter;
use Edf\Core\Logging\ContextSanitizer;
use Edf\Tests\Fixtures\Logging\CollectingLogger;
use Edf\Tests\Fixtures\SuratMasukError;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ApplicationExceptionReporterTest extends TestCase
{
    public function test_it_logs_structured_metadata_and_sanitized_context(): void
    {
        $logger = new CollectingLogger();
        $exception = new ApplicationException(
            (new ErrorDefinitionReader())->read(SuratMasukError::DRAFT_LOCKED),
            ['document_id' => 1001, 'token' => 'abc'],
        );

        (new ApplicationExceptionReporter($logger, new ContextSanitizer()))->report($exception);

        $this->assertSame([[
            'level' => 'warning',
            'message' => 'Draft sedang dikunci.',
            'context' => [
                'error_code' => 'SM-WF-001',
                'category' => 'workflow',
                'severity' => 'medium',
                'retryable' => false,
                'http_status' => 409,
                'context' => ['document_id' => 1001, 'token' => '[REDACTED]'],
                'exception' => $exception,
            ],
        ]], $logger->records);
    }

    /**
     * @return iterable<string, array{SuratMasukError, string}>
     */
    public static function severityLevels(): iterable
    {
        yield 'low' => [SuratMasukError::NOMOR_SURAT_REQUIRED, 'info'];
        yield 'medium' => [SuratMasukError::DRAFT_LOCKED, 'warning'];
        yield 'high' => [SuratMasukError::TTE_UNAVAILABLE, 'error'];
        yield 'critical' => [SuratMasukError::STORAGE_DOWN, 'critical'];
    }

    #[DataProvider('severityLevels')]
    public function test_severity_maps_to_log_level(SuratMasukError $error, string $level): void
    {
        $logger = new CollectingLogger();

        (new ApplicationExceptionReporter($logger, new ContextSanitizer()))->report(
            new ApplicationException((new ErrorDefinitionReader())->read($error)),
        );

        $this->assertSame($level, $logger->records[0]['level']);
    }
}
