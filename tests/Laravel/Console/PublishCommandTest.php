<?php

declare(strict_types=1);

namespace Edf\Tests\Laravel\Console;

use Edf\Laravel\Discovery\ErrorDefinitionDiscovery;
use Edf\Laravel\ErrorDefinitionServiceProvider;
use Edf\Tests\Fixtures\Discovery\FixtureAutoloader;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase;

final class PublishCommandTest extends TestCase
{
    private const array CATALOG = [
        'schemaVersion' => 1,
        'errors' => [[
            'code' => 'SHP-VAL-001',
            'message' => ':attribute wajib diisi.',
            'category' => 'validation',
            'httpStatus' => 422,
            'severity' => 'medium',
            'retryable' => false,
        ]],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        FixtureAutoloader::register();
        $this->useProject('warning');

        $this->app['config']->set('error-definition.publish.application', 'shipping');
        Http::preventStrayRequests();
    }

    protected function getPackageProviders($app): array
    {
        return [ErrorDefinitionServiceProvider::class];
    }

    public function test_default_config(): void
    {
        $config = (require dirname(__DIR__, 3).'/config/error-definition.php')['publish'];

        $this->assertSame('disk', $config['driver']);
        $this->assertSame('local', $config['disk']['name']);
        $this->assertSame('error-definition/{application}.json', $config['disk']['path']);
        $this->assertSame(10, $config['http']['timeout']);
    }

    public function test_disk_driver_writes_payload_file(): void
    {
        Storage::fake('shared');
        $this->app['config']->set('error-definition.publish.disk.name', 'shared');

        $this->artisan('error-definition:publish', ['--revision' => 'a1b2c3d'])
            ->expectsOutput('published  shared:error-definition/shipping.json (1 error, revision a1b2c3d)')
            ->assertExitCode(0);

        $this->assertSame(
            ['application' => 'shipping', 'revision' => 'a1b2c3d', 'catalog' => self::CATALOG],
            json_decode(Storage::disk('shared')->get('error-definition/shipping.json'), true),
        );
    }

    public function test_http_driver_posts_payload_with_token(): void
    {
        Http::fake(['catalog.internal/*' => Http::response(status: 201)]);
        $this->useHttp(token: 'rahasia');

        $this->artisan('error-definition:publish', ['--revision' => 'a1b2c3d'])
            ->expectsOutput('published  https://catalog.internal/catalogs (1 error, revision a1b2c3d)')
            ->assertExitCode(0);

        Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
            && $request->url() === 'https://catalog.internal/catalogs'
            && $request->hasHeader('Authorization', 'Bearer rahasia')
            && $request->hasHeader('Content-Type', 'application/json')
            && $request->data() === ['application' => 'shipping', 'revision' => 'a1b2c3d', 'catalog' => self::CATALOG]);
    }

    public function test_http_driver_omits_authorization_without_token(): void
    {
        Http::fake(['catalog.internal/*' => Http::response()]);
        $this->useHttp(token: null);

        $this->artisan('error-definition:publish', ['--revision' => 'a1b2c3d'])->assertExitCode(0);

        Http::assertSent(fn (Request $request): bool => ! $request->hasHeader('Authorization'));
    }

    public function test_http_rejection_fails_with_status_and_message(): void
    {
        Http::fake(['catalog.internal/*' => Http::response(['message' => 'Token tidak valid.'], 401)]);
        $this->useHttp(token: 'salah');

        $this->artisan('error-definition:publish', ['--revision' => 'a1b2c3d'])
            ->expectsOutputToContain('HTTP 401: Token tidak valid.')
            ->assertExitCode(1);
    }

    public function test_http_connection_failure_fails_the_command(): void
    {
        Http::fake(fn () => throw new ConnectionException('Connection refused'));
        $this->useHttp(token: null);

        $this->artisan('error-definition:publish', ['--revision' => 'a1b2c3d'])
            ->expectsOutputToContain('tidak dapat dihubungi: Connection refused')
            ->assertExitCode(1);
    }

    public function test_http_driver_requires_url(): void
    {
        $this->app['config']->set('error-definition.publish.driver', 'http');

        $this->artisan('error-definition:publish', ['--revision' => 'a1b2c3d'])
            ->expectsOutputToContain('publish.http.url')
            ->assertExitCode(1);
    }

    public function test_revision_is_required(): void
    {
        $this->artisan('error-definition:publish')
            ->expectsOutputToContain('--revision wajib diisi')
            ->assertExitCode(1);
    }

    public function test_application_must_be_a_slug(): void
    {
        $this->app['config']->set('error-definition.publish.application', 'Shipping App');

        $this->artisan('error-definition:publish', ['--revision' => 'a1b2c3d'])
            ->expectsOutputToContain("slug huruf kecil seperti 'surat-masuk', saat ini 'Shipping App'")
            ->assertExitCode(1);
    }

    public function test_unknown_driver_fails(): void
    {
        $this->app['config']->set('error-definition.publish.driver', 'rsync');

        $this->artisan('error-definition:publish', ['--revision' => 'a1b2c3d'])
            ->expectsOutputToContain("Driver publish 'rsync' tidak dikenal")
            ->assertExitCode(1);
    }

    public function test_definition_lint_errors_stop_publish(): void
    {
        Storage::fake('local');
        $this->useProject('app');

        $this->artisan('error-definition:publish', ['--revision' => 'a1b2c3d'])
            ->expectsOutputToContain('ERROR   EDF002  EdfDiscoveryApp\Errors\IntBackedOrderError')
            ->assertExitCode(1);

        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    private function useHttp(?string $token): void
    {
        $this->app['config']->set('error-definition.publish.driver', 'http');
        $this->app['config']->set('error-definition.publish.http.url', 'https://catalog.internal/catalogs');
        $this->app['config']->set('error-definition.publish.http.token', $token);
    }

    private function useProject(string $project): void
    {
        $this->app->instance(
            ErrorDefinitionDiscovery::class,
            new ErrorDefinitionDiscovery(FixtureAutoloader::path($project)),
        );
    }
}
