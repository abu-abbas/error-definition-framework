<?php

declare(strict_types=1);

namespace Edf\Tests\Fixtures\Lint;

use Edf\Laravel\Validation\HasErrorDefinitions;
use Edf\Tests\Fixtures\Validation\SuratMasukValidationError;
use Illuminate\Foundation\Http\FormRequest;
use RuntimeException;

final class ThrowingRulesRequest extends FormRequest
{
    use HasErrorDefinitions;

    public function rules(): array
    {
        throw new RuntimeException('Butuh route parameter.');
    }

    public function errorCodes(): array
    {
        return ['nomor_surat.required' => SuratMasukValidationError::NOMOR_SURAT_REQUIRED];
    }
}
