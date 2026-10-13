<?php

declare(strict_types=1);

namespace Edf\Tests\Laravel;

use Edf\Core\ErrorDefinitionReader;
use Edf\Core\Exceptions\ApplicationException;
use Edf\Laravel\ErrorDefinitionServiceProvider;
use Edf\Tests\Fixtures\SuratMasukError;
use Edf\Tests\Fixtures\Validation\StoreSuratMasukRequest;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use LogicException;
use Orchestra\Testbench\TestCase;

final class ErrorResponseRendererTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ErrorDefinitionServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        // Debug aktif untuk membuktikan informasi internal tetap tidak bocor.
        $app['config']->set('app.debug', true);
    }

    protected function defineRoutes($router): void
    {
        $router->post('/draft', function () {
            throw new ApplicationException(
                $this->app->make(ErrorDefinitionReader::class)->read(SuratMasukError::DRAFT_LOCKED),
                ['password' => 'rahasia'],
                new LogicException('penyebab internal'),
            );
        });

        $router->post('/tte', function () {
            throw new ApplicationException(
                $this->app->make(ErrorDefinitionReader::class)->read(SuratMasukError::TTE_UNAVAILABLE),
            );
        });

        $router->post('/surat-masuk', fn (StoreSuratMasukRequest $request) => 'ok');
    }

    public function test_application_exception_renders_public_fields_only(): void
    {
        $response = $this->postJson('/draft');

        $response->assertStatus(409);
        $this->assertSame([
            'message' => 'Draft sedang dikunci.',
            'code' => 'SM-WF-001',
            'retryable' => false,
        ], $response->json());
    }

    public function test_application_exception_uses_definition_status_and_retryable(): void
    {
        $response = $this->postJson('/tte');

        $response->assertStatus(503);
        $this->assertSame([
            'message' => 'Layanan tanda tangan elektronik tidak tersedia.',
            'code' => 'SM-INT-001',
            'retryable' => true,
        ], $response->json());
    }

    public function test_validation_exception_renders_errors_grouped_by_actual_attribute(): void
    {
        $response = $this->postJson('/surat-masuk', [
            'perihal' => 'abc',
            'attachments' => [['file' => 'a.pdf'], ['file' => '']],
        ]);

        $response->assertStatus(422);
        $this->assertSame([
            'message' => 'Nomor surat wajib diisi. (and 2 more errors)',
            'errors' => [
                'nomor_surat' => [
                    ['message' => 'Nomor surat wajib diisi.', 'code' => 'SM-VAL-001', 'retryable' => false],
                ],
                'perihal' => [
                    ['message' => 'Perihal minimal 5 karakter.', 'code' => 'SM-VAL-002', 'retryable' => false],
                ],
                'attachments.1.file' => [
                    ['message' => 'File lampiran wajib diunggah.', 'code' => 'SM-VAL-003', 'retryable' => false],
                ],
            ],
        ], $response->json());
    }

    public function test_non_json_requests_fall_back_to_laravel(): void
    {
        $this->post('/surat-masuk', [])->assertRedirect();

        $response = $this->post('/draft');

        $response->assertStatus(500);
        $this->assertStringNotContainsString('json', (string) $response->headers->get('Content-Type'));
    }

    public function test_custom_json_detection_from_application_is_respected(): void
    {
        $this->app->make(ExceptionHandler::class)->shouldRenderJsonWhen(
            fn (Request $request): bool => $request->is('draft'),
        );

        $response = $this->post('/draft');

        $response->assertStatus(409);
        $response->assertExactJson([
            'message' => 'Draft sedang dikunci.',
            'code' => 'SM-WF-001',
            'retryable' => false,
        ]);
    }
}
