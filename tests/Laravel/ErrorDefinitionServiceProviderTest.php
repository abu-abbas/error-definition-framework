<?php

declare(strict_types=1);

namespace Edf\Tests\Laravel;

use Edf\Core\ErrorDefinitionReader;
use Edf\Core\Exceptions\ApplicationException;
use Edf\Laravel\ErrorDefinitionServiceProvider;
use Edf\Tests\Fixtures\SuratMasukError;
use Orchestra\Testbench\TestCase;

final class ErrorDefinitionServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ErrorDefinitionServiceProvider::class];
    }

    public function test_reader_is_registered_as_singleton(): void
    {
        $this->assertSame(
            $this->app->make(ErrorDefinitionReader::class),
            $this->app->make(ErrorDefinitionReader::class),
        );
    }

    public function test_application_exception_of_uses_the_container_reader(): void
    {
        $definition = ApplicationException::of(SuratMasukError::DRAFT_LOCKED)->definition;

        $this->assertSame(
            $this->app->make(ErrorDefinitionReader::class)->read(SuratMasukError::DRAFT_LOCKED),
            $definition,
        );
    }
}
