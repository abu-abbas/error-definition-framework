<?php

declare(strict_types=1);

namespace Edf\Tests\Unit;

use Edf\Core\ErrorDefinitionReader;
use Edf\Core\Exceptions\ApplicationException;
use Edf\Core\Exceptions\MissingErrorDefinitionException;
use Edf\Tests\Fixtures\SuratMasukError;
use LogicException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;

final class ApplicationExceptionTest extends TestCase
{
    public function test_it_carries_definition_and_takes_message_from_it(): void
    {
        $definition = (new ErrorDefinitionReader())->read(SuratMasukError::DRAFT_LOCKED);

        $exception = new ApplicationException($definition);

        $this->assertInstanceOf(RuntimeException::class, $exception);
        $this->assertSame($definition, $exception->definition);
        $this->assertSame('Draft sedang dikunci.', $exception->getMessage());
        $this->assertSame('SM-WF-001', $exception->definition->code);
        $this->assertSame(0, $exception->getCode());
        $this->assertSame([], $exception->context);
        $this->assertNull($exception->getPrevious());
    }

    public function test_it_keeps_context_unsanitized_and_previous_exception(): void
    {
        $definition = (new ErrorDefinitionReader())->read(SuratMasukError::TTE_UNAVAILABLE);
        $previous = new LogicException('timeout');
        $context = ['surat_id' => 42, 'password' => 'rahasia'];

        $exception = new ApplicationException($definition, $context, $previous);

        $this->assertSame($context, $exception->context);
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function test_it_is_final_so_errors_vary_by_enum_not_subclass(): void
    {
        $this->assertTrue((new ReflectionClass(ApplicationException::class))->isFinal());
    }

    public function test_of_resolves_enum_case_and_keeps_context_and_previous(): void
    {
        $previous = new LogicException('penyebab');

        $exception = ApplicationException::of(SuratMasukError::TTE_UNAVAILABLE, ['surat_id' => 7], $previous);

        $this->assertSame('SM-INT-001', $exception->definition->code);
        $this->assertTrue($exception->definition->retryable);
        $this->assertSame('Layanan tanda tangan elektronik tidak tersedia.', $exception->getMessage());
        $this->assertSame(['surat_id' => 7], $exception->context);
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function test_of_fails_for_case_without_definition(): void
    {
        $this->expectException(MissingErrorDefinitionException::class);

        ApplicationException::of(SuratMasukError::UNDEFINED);
    }

    public function test_of_uses_the_registered_reader(): void
    {
        $reader = new ErrorDefinitionReader();
        ApplicationException::useReader($reader);

        $definition = ApplicationException::of(SuratMasukError::DRAFT_LOCKED)->definition;

        $this->assertSame($reader->read(SuratMasukError::DRAFT_LOCKED), $definition);
    }
}
