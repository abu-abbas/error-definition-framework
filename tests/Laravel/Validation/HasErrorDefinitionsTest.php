<?php

declare(strict_types=1);

namespace Edf\Tests\Laravel\Validation;

use Edf\Laravel\ErrorDefinitionServiceProvider;
use Edf\Laravel\Validation\ErrorValidationException;
use Edf\Laravel\Validation\InvalidValidationErrorDefinitionException;
use Edf\Laravel\Validation\MissingValidationErrorCodeException;
use Edf\Laravel\Validation\ResolvedValidationError;
use Edf\Tests\Fixtures\Validation\NonValidationDefinitionRequest;
use Edf\Tests\Fixtures\Validation\StoreSuratMasukRequest;
use Edf\Tests\Fixtures\Validation\UnmappedRuleRequest;
use Edf\Tests\Fixtures\Validation\Uppercase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidationException;
use Orchestra\Testbench\TestCase;

final class HasErrorDefinitionsTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ErrorDefinitionServiceProvider::class];
    }

    public function test_valid_request_passes(): void
    {
        $this->validate(StoreSuratMasukRequest::class, [
            'nomor_surat' => 'SM/001',
            'perihal' => 'Undangan rapat',
            'kode' => 'ABC',
            'attachments' => [['file' => 'a.pdf']],
        ]);

        $this->addToAssertionCount(1);
    }

    public function test_failures_are_mapped_to_error_definitions(): void
    {
        $exception = $this->expectValidationFailure(StoreSuratMasukRequest::class, [
            'perihal' => 'abc',
            'kode' => 'abc',
            'attachments' => [['file' => 'a.pdf'], ['file' => '']],
            'jadwal' => 'penuh',
        ]);

        $this->assertInstanceOf(ValidationException::class, $exception);
        $this->assertSame('surat', $exception->errorBag);

        $this->assertSame([
            ['nomor_surat', 'required', 'SM-VAL-001', 'Nomor surat wajib diisi.'],
            ['perihal', 'min', 'SM-VAL-002', 'Perihal minimal 5 karakter.'],
            ['kode', Uppercase::class, 'SM-VAL-004', 'Kode harus huruf kapital.'],
            ['attachments.1.file', 'required', 'SM-VAL-003', 'File lampiran wajib diunggah.'],
            ['jadwal', 'available', 'SM-VAL-005', 'Jadwal tidak tersedia.'],
        ], array_map(
            fn (ResolvedValidationError $error): array => [
                $error->attribute,
                $error->rule,
                $error->definition->code,
                $error->message,
            ],
            $exception->resolvedErrors,
        ));
    }

    public function test_laravel_error_bag_uses_error_definition_messages(): void
    {
        $exception = $this->expectValidationFailure(StoreSuratMasukRequest::class, [
            'perihal' => 'abc',
            'kode' => 'abc',
        ]);

        $this->assertSame([
            'nomor_surat' => ['Nomor surat wajib diisi.'],
            'perihal' => ['Perihal minimal 5 karakter.'],
            'kode' => ['Kode harus huruf kapital.'],
        ], $exception->errors());
    }

    public function test_bail_still_stops_after_first_failure(): void
    {
        $exception = $this->expectValidationFailure(StoreSuratMasukRequest::class, [
            'nomor_surat' => 'SM/001',
            'perihal' => ['bukan string'],
        ]);

        $this->assertSame(['perihal.string'], array_map(
            fn (ResolvedValidationError $error): string => $error->attribute.'.'.$error->rule,
            $exception->resolvedErrors,
        ));
    }

    public function test_unmapped_failed_rule_throws(): void
    {
        $this->expectException(MissingValidationErrorCodeException::class);
        $this->expectExceptionMessage('"email.email"');

        $this->validate(UnmappedRuleRequest::class, ['email' => 'bukan-email']);
    }

    public function test_non_validation_definition_is_rejected(): void
    {
        $this->expectException(InvalidValidationErrorDefinitionException::class);
        $this->expectExceptionMessage('SM-WF-001');

        $this->validate(NonValidationDefinitionRequest::class, []);
    }

    /**
     * @param class-string<FormRequest> $request
     * @param array<string, mixed> $data
     */
    private function expectValidationFailure(string $request, array $data): ErrorValidationException
    {
        try {
            $this->validate($request, $data);
        } catch (ErrorValidationException $exception) {
            return $exception;
        }

        $this->fail('ErrorValidationException tidak dilempar.');
    }

    /**
     * @param class-string<FormRequest> $request
     * @param array<string, mixed> $data
     */
    private function validate(string $request, array $data): void
    {
        $request = $request::create('/surat-masuk', 'POST', $data)
            ->setContainer($this->app)
            ->setRedirector($this->app->make(Redirector::class));

        $request->validateResolved();
    }
}
