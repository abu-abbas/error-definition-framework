<?php

declare(strict_types=1);

namespace Edf\Tests\Laravel;

use Edf\Core\ErrorDefinitionReader;
use Edf\Laravel\ErrorDefinitionServiceProvider;
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
}
