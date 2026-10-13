<?php

declare(strict_types=1);

namespace Edf\Tests\Laravel\Console;

use Edf\Laravel\Discovery\ErrorDefinitionDiscovery;
use Edf\Laravel\ErrorDefinitionServiceProvider;
use Edf\Tests\Fixtures\Discovery\FixtureAutoloader;
use Orchestra\Testbench\TestCase;

final class GenerateCommandTest extends TestCase
{
    private string $output;

    protected function setUp(): void
    {
        parent::setUp();

        FixtureAutoloader::register();

        $this->output = sys_get_temp_dir().'/edf-generate-'.bin2hex(random_bytes(4));
        $this->app['config']->set('error-definition.generation.typescript_path', $this->output.'/js/error-codes.ts');
        $this->app['config']->set('error-definition.generation.catalog_path', $this->output.'/catalog/error-catalog.json');
    }

    protected function tearDown(): void
    {
        exec('rm -rf '.escapeshellarg($this->output));

        parent::tearDown();
    }

    protected function getPackageProviders($app): array
    {
        return [ErrorDefinitionServiceProvider::class];
    }

    public function test_default_output_paths(): void
    {
        $config = (require dirname(__DIR__, 3).'/config/error-definition.php')['generation'];

        $this->assertSame(resource_path('js/generated/error-codes.ts'), $config['typescript_path']);
        $this->assertSame(storage_path('app/error-definition/error-catalog.json'), $config['catalog_path']);
    }

    public function test_generates_typescript_then_reports_unchanged(): void
    {
        $this->useProject('warning');
        $path = $this->output.'/js/error-codes.ts';

        $this->artisan('error-definition:generate')
            ->expectsOutputToContain('generated  '.$path)
            ->assertExitCode(0);

        $this->assertStringContainsString("  'SHP_VAL_001': 'SHP-VAL-001',\n", file_get_contents($path));
        $this->assertSame([
            'schemaVersion' => 1,
            'errors' => [[
                'code' => 'SHP-VAL-001',
                'message' => ':attribute wajib diisi.',
                'category' => 'validation',
                'httpStatus' => 422,
                'severity' => 'medium',
                'retryable' => false,
            ]],
        ], json_decode(file_get_contents($this->output.'/catalog/error-catalog.json'), true));

        $this->artisan('error-definition:generate')
            ->expectsOutputToContain('unchanged  '.$path)
            ->expectsOutputToContain('unchanged  '.$this->output.'/catalog/error-catalog.json')
            ->assertExitCode(0);
    }

    public function test_check_mode_reports_missing_and_stale_without_writing(): void
    {
        $this->useProject('warning');
        $path = $this->output.'/js/error-codes.ts';

        $this->artisan('error-definition:generate', ['--check' => true])
            ->expectsOutputToContain('stale       '.$path)
            ->expectsOutputToContain('2 stale artifacts')
            ->assertExitCode(1);

        $this->assertFileDoesNotExist($path);

        $this->artisan('error-definition:generate')->assertExitCode(0);
        $this->artisan('error-definition:generate', ['--check' => true])
            ->expectsOutputToContain('up to date  '.$path)
            ->assertExitCode(0);

        file_put_contents($path, "// diedit manual\n");

        $this->artisan('error-definition:generate', ['--check' => true])
            ->expectsOutputToContain('up to date  '.$this->output.'/catalog/error-catalog.json')
            ->expectsOutputToContain('1 stale artifact')
            ->assertExitCode(1);
        $this->assertSame("// diedit manual\n", file_get_contents($path));
    }

    public function test_artifacts_must_use_different_paths(): void
    {
        $this->useProject('warning');
        $this->app['config']->set('error-definition.generation.catalog_path', $this->output.'/js/error-codes.ts');

        $this->artisan('error-definition:generate')
            ->expectsOutputToContain('catalog_path')
            ->assertExitCode(1);

        $this->assertDirectoryDoesNotExist($this->output);
    }

    public function test_definition_lint_errors_stop_generation(): void
    {
        $this->useProject('app');

        $this->artisan('error-definition:generate')
            ->expectsOutputToContain('ERROR   EDF002  EdfDiscoveryApp\Errors\IntBackedOrderError')
            ->expectsOutputToContain('artifact tidak dihasilkan')
            ->assertExitCode(1);

        $this->assertDirectoryDoesNotExist($this->output);
    }

    public function test_discovery_failure_fails_the_command(): void
    {
        $this->useProject('failing');

        $this->artisan('error-definition:generate')
            ->expectsOutputToContain('EdfDiscoveryFailing\BrokenParent')
            ->assertExitCode(1);
    }

    private function useProject(string $project): void
    {
        $this->app->instance(
            ErrorDefinitionDiscovery::class,
            new ErrorDefinitionDiscovery(FixtureAutoloader::path($project)),
        );
    }
}
