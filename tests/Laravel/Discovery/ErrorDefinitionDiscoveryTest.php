<?php

declare(strict_types=1);

namespace Edf\Tests\Laravel\Discovery;

use Edf\Core\Discovery\DiscoveryException;
use Edf\Laravel\Discovery\ErrorDefinitionDiscovery;
use Edf\Laravel\ErrorDefinitionServiceProvider;
use Edf\Tests\Fixtures\Discovery\FixtureAutoloader;
use Error;
use Orchestra\Testbench\TestCase;

final class ErrorDefinitionDiscoveryTest extends TestCase
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

    public function test_targets_come_from_root_autoload_sorted_and_by_type_contract(): void
    {
        $result = $this->discovery('app')->discover();

        $this->assertSame([
            'EdfDiscoveryApp\Errors\IntBackedOrderError',
            'EdfDiscoveryApp\Errors\OrderError',
            'LegacyInvoiceError',
        ], $result->errorEnums);

        $this->assertSame([
            'EdfDiscoveryApp\Http\Requests\StoreOrderRequest',
            'EdfDiscoveryApp\Http\Requests\UpdateOrderRequest',
        ], $result->formRequests);
    }

    public function test_autoload_dev_is_not_loaded(): void
    {
        $this->discovery('app')->discover();

        $this->assertFalse(enum_exists('EdfDiscoveryApp\Tests\FakeOrderError', false));
    }

    public function test_result_is_snapshotted_per_instance(): void
    {
        $discovery = $this->discovery('app');

        $this->assertSame($discovery->discover(), $discovery->discover());
    }

    public function test_container_resolves_singleton_for_application_base_path(): void
    {
        $this->assertSame(
            $this->app->make(ErrorDefinitionDiscovery::class),
            $this->app->make(ErrorDefinitionDiscovery::class),
        );
    }

    public function test_missing_composer_json_fails(): void
    {
        $this->expectException(DiscoveryException::class);
        $this->expectExceptionMessage('tidak ditemukan');

        $this->discovery('does-not-exist')->discover();
    }

    public function test_invalid_composer_json_fails(): void
    {
        $this->expectException(DiscoveryException::class);
        $this->expectExceptionMessage('tidak valid');

        $this->discovery('invalid')->discover();
    }

    public function test_unreadable_autoload_path_fails(): void
    {
        $this->expectException(DiscoveryException::class);
        $this->expectExceptionMessage('does-not-exist/');

        $this->discovery('missing-path')->discover();
    }

    public function test_autoload_failure_is_wrapped_with_class_and_path(): void
    {
        try {
            $this->discovery('failing')->discover();
            $this->fail('DiscoveryException tidak dilempar.');
        } catch (DiscoveryException $e) {
            $this->assertStringContainsString('EdfDiscoveryFailing\BrokenParent', $e->getMessage());
            $this->assertStringContainsString('BrokenParent.php', $e->getMessage());
            $this->assertInstanceOf(Error::class, $e->getPrevious());
        }
    }

    private function discovery(string $project): ErrorDefinitionDiscovery
    {
        return new ErrorDefinitionDiscovery(FixtureAutoloader::path($project));
    }
}
