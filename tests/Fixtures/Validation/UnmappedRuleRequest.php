<?php

declare(strict_types=1);

namespace Edf\Tests\Fixtures\Validation;

use Edf\Laravel\Validation\HasErrorDefinitions;
use Illuminate\Foundation\Http\FormRequest;

final class UnmappedRuleRequest extends FormRequest
{
    use HasErrorDefinitions;

    public function rules(): array
    {
        return ['email' => ['required', 'email']];
    }

    public function errorCodes(): array
    {
        return ['email.required' => SuratMasukValidationError::NOMOR_SURAT_REQUIRED];
    }
}
