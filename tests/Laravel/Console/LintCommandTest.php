<?php

declare(strict_types=1);

namespace Edf\Tests\Laravel\Console;

use Edf\Laravel\Discovery\ErrorDefinitionDiscovery;
use Edf\Laravel\ErrorDefinitionServiceProvider;
use Edf\Tests\Fixtures\Discovery\FixtureAutoloader;
use Orchestra\Testbench\TestCase;

final class LintCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        FixtureAutoloader::register();
    }

    protected function getPackageProviders($app): array
    {
        return [ErrorDefinitionServiceProvider::class];
    }

    public function test_errors_fail_the_command(): void
    {
        $this->useProject('app');

        $this->artisan('error-definition:lint')
            ->expectsOutputToContain('ERROR   EDF002  EdfDiscoveryApp\Errors\IntBackedOrderError')
            ->expectsOutputToContain('Enum error wajib menggunakan string backing.')
            ->expectsOutputToContain('1 error, 0 warnings')
            ->assertExitCode(1);
    }

    public function test_warnings_pass_by_default(): void
    {
        $this->useProject('warning');

        $this->artisan('error-definition:lint')
            ->expectsOutputToContain('WARNING VAL007  EdfDiscoveryWarning\StoreShippingRequest')
            ->expectsOutputToContain('0 errors, 1 warning')
            ->assertExitCode(0);
    }

    public function test_strict_mode_fails_on_warnings(): void
    {
        $this->useProject('warning');

        $this->artisan('error-definition:lint', ['--strict' => true])->assertExitCode(1);
    }

    private function useProject(string $project): void
    {
        $this->app->instance(
            ErrorDefinitionDiscovery::class,
            new ErrorDefinitionDiscovery(FixtureAutoloader::path($project)),
        );
    }
}
