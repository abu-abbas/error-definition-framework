# Menggunakan Error Definition pada Validation

[← Kembali ke Guides](./README.md)

Panduan ini menerapkan [Decision 1.5](../decisions/1-error-definition-framework/1.5-validation-error-mapping.md).

## FormRequest

```php
final class StoreSuratMasukRequest extends FormRequest
{
    use HasErrorDefinitions;

    public function rules(): array
    {
        return [
            'nomor_surat' => ['required'],
            'attachments.*.file' => ['required', 'file'],
        ];
    }

    public function errorCodes(): array
    {
        return [
            'nomor_surat.required'
                => SuratMasukError::NOMOR_SURAT_REQUIRED,

            'attachments.*.file.required'
                => SuratMasukError::ATTACHMENT_REQUIRED,

            'attachments.*.file.file'
                => SuratMasukError::ATTACHMENT_INVALID,
        ];
    }

    public function attributes(): array
    {
        return [
            'nomor_surat' => 'Nomor surat',
            'attachments.*.file' => 'File lampiran',
        ];
    }
}
```

Request yang menggunakan `HasErrorDefinitions` tidak mendefinisikan `messages()`. Message berasal dari Error Definition dan placeholder diproses oleh Laravel.

## Custom Rule

```php
public function rules(): array
{
    return [
        'name' => ['required', new Uppercase],
    ];
}

public function errorCodes(): array
{
    return [
        'name.required' => UserError::NAME_REQUIRED,
        'name.'.Uppercase::class => UserError::NAME_MUST_BE_UPPERCASE,
    ];
}
```

## Validation Tambahan

```php
public function after(): array
{
    return [
        function (Validator $validator): void {
            if ($this->scheduleIsUnavailable()) {
                $this->addValidationError(
                    validator: $validator,
                    attribute: 'schedule',
                    rule: 'available',
                    error: SuratMasukError::SCHEDULE_UNAVAILABLE,
                );
            }
        },
    ];
}
```

Gunakan `bail` atau stop-on-first-failure milik Laravel jika jumlah failure perlu dibatasi. Framework tidak mengubah behavior tersebut.
