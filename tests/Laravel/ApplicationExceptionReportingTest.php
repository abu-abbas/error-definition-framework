<?php

declare(strict_types=1);

namespace Edf\Tests\Laravel;

use Edf\Core\ErrorDefinitionReader;
use Edf\Core\Exceptions\ApplicationException;
use Edf\Laravel\ErrorDefinitionServiceProvider;
use Edf\Tests\Fixtures\Logging\CollectingLogger;
use Edf\Tests\Fixtures\SuratMasukError;
use Edf\Tests\Fixtures\Validation\StoreSuratMasukRequest;
use Psr\Log\LoggerInterface;
use Orchestra\Testbench\TestCase;

final class ApplicationExceptionReportingTest extends TestCase
{
    private CollectingLogger $logger;

    protected function getPackageProviders($app): array
    {
        return [ErrorDefinitionServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('error-definition.logging.additional_sensitive_keys', ['nik']);

        $this->logger = new CollectingLogger();
        $app->instance(LoggerInterface::class, $this->logger);
    }

    protected function defineRoutes($router): void
    {
        $router->post('/draft', function () {
            throw new ApplicationException(
                $this->app->make(ErrorDefinitionReader::class)->read(SuratMasukError::DRAFT_LOCKED),
                ['document_id' => 1001, 'nik' => '3273', 'password' => 'rahasia'],
            );
        });

        $router->post('/surat-masuk', fn (StoreSuratMasukRequest $request) => 'ok');
    }

    public function test_application_exception_is_logged_exactly_once_with_sanitized_context(): void
    {
        $this->postJson('/draft')->assertStatus(409);

        $this->assertCount(1, $this->logger->records);
        $this->assertSame('warning', $this->logger->records[0]['level']);
        $this->assertSame('Draft sedang dikunci.', $this->logger->records[0]['message']);
        $this->assertSame('SM-WF-001', $this->logger->records[0]['context']['error_code']);
        $this->assertSame(
            ['document_id' => 1001, 'nik' => '[REDACTED]', 'password' => '[REDACTED]'],
            $this->logger->records[0]['context']['context'],
        );
    }

    public function test_validation_failures_are_not_logged(): void
    {
        $this->postJson('/surat-masuk', [])->assertStatus(422);

        $this->assertSame([], $this->logger->records);
    }
}
