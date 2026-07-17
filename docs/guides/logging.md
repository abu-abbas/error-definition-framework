# Logging dan Context

[← Kembali ke Guides](./README.md)

Panduan ini menerapkan [Decision 1.7](../decisions/1-error-definition-framework/1.7-logging-dan-context.md).

## Menambahkan Runtime Context

Berikan identifier minimum yang membantu tim menemukan kejadian terkait:

```php
$exception = new ApplicationException(
    definition: $reader->read(
        SuratMasukError::DRAFT_LOCKED,
    ),
    context: [
        'user_id' => $user->id,
        'document_id' => $surat->id,
        'request_id' => $request->header('X-Request-ID'),
    ],
);

throw $exception;
```

Reporter akan menghasilkan structured context berikut:

```php
[
    'error_code' => 'SM-WF-001',
    'category' => 'workflow',
    'severity' => 'medium',
    'retryable' => false,
    'http_status' => 409,
    'context' => [
        'user_id' => 42,
        'document_id' => 1001,
        'request_id' => 'req-01J...',
    ],
    'exception' => $exception,
]
```

Gunakan scalar identifier. Jangan memasukkan seluruh model, Request, User, session, atau payload jika beberapa identifier sudah cukup untuk investigasi.

## Menambahkan Sensitive Key

Credential umum sudah dilindungi oleh package. Tambahkan nama field khusus domain melalui config:

```php
// config/error-definition.php

return [
    'logging' => [
        'additional_sensitive_keys' => [
            'nik',
            'bank_account',
            'card_number',
            'salary',
        ],
    ],
];
```

Rule berlaku pada setiap tingkat nested array:

```php
[
    'employee' => [
        'id' => 42,
        'nik' => '3273...',
    ],
    'accessToken' => 'secret',
]
```

Setelah sanitasi:

```php
[
    'employee' => [
        'id' => 42,
        'nik' => '[REDACTED]',
    ],
    'accessToken' => '[REDACTED]',
]
```

Config tambahan tidak menggantikan baseline package. Hindari menaruh credential atau data pribadi di dalam exception message karena sanitizer hanya memproses runtime context.

## Monitoring

Gunakan konfigurasi logging Laravel untuk meneruskan event ke channel atau monitoring provider yang digunakan aplikasi. Package tidak memerlukan integration khusus untuk setiap provider.
