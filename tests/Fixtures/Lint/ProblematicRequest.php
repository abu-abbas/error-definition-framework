<?php

declare(strict_types=1);

namespace Edf\Tests\Fixtures\Lint;

use Closure;
use Edf\Laravel\Validation\HasErrorDefinitions;
use Edf\Tests\Fixtures\SuratMasukError;
use Edf\Tests\Fixtures\Validation\SuratMasukValidationError as V;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ProblematicRequest extends FormRequest
{
    use HasErrorDefinitions;

    public function rules(): array
    {
        return [
            'nama' => 'required|max:50',
            'tags.*' => ['string', fn (string $attribute, mixed $value, Closure $fail) => null],
            'jenis' => [Rule::when(true, ['required'])],
            'status' => ['sometimes', Rule::in(['draft', 'final'])],
            'tanggal' => [Rule::date()],
            'kode' => [new class implements ValidationRule
            {
                public function validate(string $attribute, mixed $value, Closure $fail): void {}
            }],
            'catatan' => ['nullable', 'string'],
        ];
    }

    public function errorCodes(): array
    {
        return [
            'nama.required' => V::NOMOR_SURAT_REQUIRED,
            'nama.max' => V::PERIHAL_TOO_SHORT,
            'tags.*.string' => V::PERIHAL_MUST_BE_STRING,
            'status.in' => V::KODE_MUST_BE_UPPERCASE,
            'tanggal.date' => SuratMasukError::UNDEFINED,
            'jenis.required' => V::NOMOR_SURAT_REQUIRED,
            'nama' => V::NOMOR_SURAT_REQUIRED,
            'nama.Required' => V::NOMOR_SURAT_REQUIRED,
            'alamat.required' => V::NOMOR_SURAT_REQUIRED,
            'catatan.string' => 'SM-VAL-006',
        ];
    }

    public function messages(): array
    {
        return [];
    }
}
