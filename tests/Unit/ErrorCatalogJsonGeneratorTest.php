<?php

declare(strict_types=1);

namespace Edf\Tests\Unit;

use Edf\Core\ErrorCategory;
use Edf\Core\ErrorSeverity;
use Edf\Core\Generation\ErrorCatalogJsonGenerator;
use Edf\Core\Generation\GenerationException;
use Edf\Core\ResolvedErrorDefinition;
use PHPUnit\Framework\TestCase;

final class ErrorCatalogJsonGeneratorTest extends TestCase
{
    public function test_generates_sorted_catalog_with_two_space_indent(): void
    {
        $content = (new ErrorCatalogJsonGenerator())->generate([
            new ResolvedErrorDefinition('SM-WF-001', 'Draft sedang dikunci.', ErrorCategory::WORKFLOW, 409, ErrorSeverity::MEDIUM, false),
            new ResolvedErrorDefinition('SM-INT-001', 'Layanan TTE/BSrE tidak tersedia — coba lagi.', ErrorCategory::INTEGRATION, 503, ErrorSeverity::HIGH, true),
            new ResolvedErrorDefinition('SM-VAL-002', ':attribute minimal :min karakter.', ErrorCategory::VALIDATION, 422, ErrorSeverity::LOW, false),
        ]);

        $this->assertSame(<<<'JSON'
            {
              "schemaVersion": 1,
              "errors": [
                {
                  "code": "SM-INT-001",
                  "message": "Layanan TTE/BSrE tidak tersedia — coba lagi.",
                  "category": "integration",
                  "httpStatus": 503,
                  "severity": "high",
                  "retryable": true
                },
                {
                  "code": "SM-VAL-002",
                  "message": ":attribute minimal :min karakter.",
                  "category": "validation",
                  "httpStatus": 422,
                  "severity": "low",
                  "retryable": false
                },
                {
                  "code": "SM-WF-001",
                  "message": "Draft sedang dikunci.",
                  "category": "workflow",
                  "httpStatus": 409,
                  "severity": "medium",
                  "retryable": false
                }
              ]
            }

            JSON, $content);
    }

    public function test_empty_catalog(): void
    {
        $this->assertSame(
            "{\n  \"schemaVersion\": 1,\n  \"errors\": []\n}\n",
            (new ErrorCatalogJsonGenerator())->generate([]),
        );
    }

    public function test_message_indentation_is_not_altered(): void
    {
        $content = (new ErrorCatalogJsonGenerator())->generate([
            new ResolvedErrorDefinition('SM-WF-002', "Baris satu\n    baris dua", ErrorCategory::WORKFLOW, 409, ErrorSeverity::MEDIUM, false),
        ]);

        $this->assertSame("Baris satu\n    baris dua", json_decode($content, true)['errors'][0]['message']);
    }

    public function test_encoding_failure_throws(): void
    {
        $this->expectException(GenerationException::class);
        $this->expectExceptionMessage('gagal di-encode');

        (new ErrorCatalogJsonGenerator())->generate([
            new ResolvedErrorDefinition('SM-WF-003', "\xB1\x31", ErrorCategory::WORKFLOW, 409, ErrorSeverity::MEDIUM, false),
        ]);
    }
}
