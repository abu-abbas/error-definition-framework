<?php

declare(strict_types=1);

namespace Edf\Tests\Unit;

use Edf\Core\ErrorCategory;
use Edf\Core\ErrorDefinitionReader;
use Edf\Core\ErrorSeverity;
use Edf\Core\Exceptions\MissingErrorDefinitionException;
use Edf\Core\ResolvedErrorDefinition;
use Edf\Tests\Fixtures\SuratMasukError;
use PHPUnit\Framework\TestCase;

final class ErrorDefinitionReaderTest extends TestCase
{
    public function test_it_resolves_code_and_metadata(): void
    {
        $definition = (new ErrorDefinitionReader())->read(SuratMasukError::TTE_UNAVAILABLE);

        $this->assertEquals(new ResolvedErrorDefinition(
            code: 'SM-INT-001',
            message: 'Layanan tanda tangan elektronik tidak tersedia.',
            category: ErrorCategory::INTEGRATION,
            httpStatus: 503,
            severity: ErrorSeverity::HIGH,
            retryable: true,
        ), $definition);
    }

    public function test_it_applies_attribute_defaults(): void
    {
        $definition = (new ErrorDefinitionReader())->read(SuratMasukError::DRAFT_LOCKED);

        $this->assertSame(ErrorSeverity::MEDIUM, $definition->severity);
        $this->assertFalse($definition->retryable);
    }

    public function test_it_caches_resolved_definitions(): void
    {
        $reader = new ErrorDefinitionReader();

        $this->assertSame(
            $reader->read(SuratMasukError::DRAFT_LOCKED),
            $reader->read(SuratMasukError::DRAFT_LOCKED),
        );
        $this->assertNotSame(
            $reader->read(SuratMasukError::DRAFT_LOCKED),
            $reader->read(SuratMasukError::NOMOR_SURAT_REQUIRED),
        );
    }

    public function test_it_fails_fast_when_attribute_is_missing(): void
    {
        $this->expectException(MissingErrorDefinitionException::class);
        $this->expectExceptionMessage(SuratMasukError::class.'::UNDEFINED');

        (new ErrorDefinitionReader())->read(SuratMasukError::UNDEFINED);
    }
}
