<?php

declare(strict_types=1);

namespace Edf\Tests\Fixtures\Validation;

use Edf\Laravel\Validation\HasErrorDefinitions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class StoreSuratMasukRequest extends FormRequest
{
    use HasErrorDefinitions;

    protected $errorBag = 'surat';

    public function rules(): array
    {
        return [
            'nomor_surat' => ['required'],
            'perihal' => ['bail', 'string', 'min:5'],
            'kode' => ['nullable', new Uppercase()],
            'attachments.*.file' => ['required'],
        ];
    }

    public function errorCodes(): array
    {
        return [
            'nomor_surat.required' => SuratMasukValidationError::NOMOR_SURAT_REQUIRED,
            'perihal.string' => SuratMasukValidationError::PERIHAL_MUST_BE_STRING,
            'perihal.min' => SuratMasukValidationError::PERIHAL_TOO_SHORT,
            'kode.'.Uppercase::class => SuratMasukValidationError::KODE_MUST_BE_UPPERCASE,
            'attachments.*.file.required' => SuratMasukValidationError::ATTACHMENT_REQUIRED,
        ];
    }

    public function attributes(): array
    {
        return [
            'nomor_surat' => 'Nomor surat',
            'perihal' => 'Perihal',
            'kode' => 'Kode',
            'attachments.*.file' => 'File lampiran',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($this->input('jadwal') === 'penuh') {
                    $this->addValidationError(
                        validator: $validator,
                        attribute: 'jadwal',
                        rule: 'available',
                        error: SuratMasukValidationError::SCHEDULE_UNAVAILABLE,
                    );
                }
            },
        ];
    }
}
