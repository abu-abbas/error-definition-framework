# Menggunakan Error Definition pada Validation

[← Kembali ke Guides](./README.md)

Validation menggunakan enum error yang sama dengan exception dan response. Error code maupun message tidak didefinisikan ulang di FormRequest.

```php
public function errorCodes(): array
{
    return [
        'nomor_surat.required' => SuratMasukError::NOMOR_SURAT_REQUIRED,
    ];
}
```

Message validation diambil dari `ErrorDefinition`. Category dan severity tetap tersedia melalui hasil resolve Reader sehingga seluruh consumer menggunakan definisi yang sama.

Integrasi lengkap FormRequest akan dibahas pada decision dan guide Stage 1.5.
