<?php

declare(strict_types=1);

namespace Edf\Tests\Laravel;

use Edf\Core\ErrorDefinitionReader;
use Edf\Core\Exceptions\ApplicationException;
use Edf\Laravel\ErrorDefinitionServiceProvider;
use Edf\Tests\Fixtures\Handler\DecoratingExceptionHandler;
use Edf\Tests\Fixtures\Handler\DecoratingHandlerServiceProvider;
use Edf\Tests\Fixtures\Logging\CollectingLogger;
use Edf\Tests\Fixtures\SuratMasukError;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Log\LoggerInterface;

/**
 * Artisan command dan queue worker di aplikasi dengan Collision memakai handler
 * yang dibungkus decorator; reporter tetap harus dipakai.
 */
final class DecoratedExceptionHandlerTest extends TestCase
{
    private CollectingLogger $logger;

    #[DataProvider('providerOrders')]
    public function test_reporter_is_used_when_handler_is_decorated(array $providers): void
    {
        $this->providers = $providers;
        $this->refreshApplication();

        $this->assertInstanceOf(DecoratingExceptionHandler::class, $this->app->make(ExceptionHandler::class));

        report(new ApplicationException(
            $this->app->make(ErrorDefinitionReader::class)->read(SuratMasukError::DRAFT_LOCKED),
            ['password' => 'rahasia'],
        ));

        $this->assertCount(1, $this->logger->records);
        $this->assertSame('warning', $this->logger->records[0]['level']);
        $this->assertSame('SM-WF-001', $this->logger->records[0]['context']['error_code']);
        $this->assertSame(['password' => '[REDACTED]'], $this->logger->records[0]['context']['context']);
    }

    /**
     * @return array<string, array{list<class-string>}>
     */
    public static function providerOrders(): array
    {
        return [
            'decorator registered first' => [[DecoratingHandlerServiceProvider::class, ErrorDefinitionServiceProvider::class]],
            'decorator registered last' => [[ErrorDefinitionServiceProvider::class, DecoratingHandlerServiceProvider::class]],
        ];
    }

    /** @var list<class-string> */
    private array $providers = [ErrorDefinitionServiceProvider::class];

    protected function getPackageProviders($app): array
    {
        return $this->providers;
    }

    protected function defineEnvironment($app): void
    {
        $this->logger = new CollectingLogger();
        $app->instance(LoggerInterface::class, $this->logger);
    }
}
