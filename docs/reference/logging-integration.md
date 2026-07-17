# Logging Integration Reference

[← Kembali ke Reference](./README.md)

## ApplicationExceptionReporter

```php
public function report(
    ApplicationException $exception,
): void
```

Reporter mencatat satu structured log event menggunakan default Laravel logger.

### Log Level

| Severity | Level |
|----------|-------|
| `LOW` | `info` |
| `MEDIUM` | `warning` |
| `HIGH` | `error` |
| `CRITICAL` | `critical` |

### Log Context

```php
[
    'error_code' => $exception->definition->code,
    'category' => $exception->definition->category->value,
    'severity' => $exception->definition->severity->value,
    'retryable' => $exception->definition->retryable,
    'http_status' => $exception->definition->httpStatus,
    'context' => $sanitizer->sanitize($exception->context),
    'exception' => $exception,
]
```

Message log menggunakan `$exception->definition->message`. Reporter tidak menangani `ErrorValidationException` dan menghentikan default reporting untuk mencegah duplicate log.

## ContextSanitizer

```php
/**
 * @param array<string, mixed> $context
 * @return array<string, mixed>
 */
public function sanitize(array $context): array
```

Sanitizer memproses nested array dan mengganti value pada sensitive key dengan `[REDACTED]`.

### Default Sensitive Keys

```text
password
password_confirmation
current_password
token
access_token
refresh_token
authorization
cookie
secret
api_key
client_secret
private_key
```

Matching bersifat case-insensitive setelah karakter selain huruf dan angka dihapus. Aplikasi dapat menambahkan key, tetapi tidak menghapus daftar bawaan.

### Normalization

| Input | Hasil |
|-------|-------|
| scalar atau `null` | dipertahankan |
| array | diproses secara recursive |
| `BackedEnum` | menggunakan `value` |
| `DateTimeInterface` | string ISO 8601 |
| arbitrary object | penanda yang memuat nama tipe |
| resource | penanda yang memuat jenis resource |

## Configuration

```php
// config/error-definition.php

return [
    'logging' => [
        'additional_sensitive_keys' => [],
    ],
];
```

Config hanya menerima tambahan nama key. Replacement `[REDACTED]`, aturan normalisasi, dan baseline sensitive keys merupakan kontrak package.
