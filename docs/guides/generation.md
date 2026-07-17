# Menghasilkan TypeScript dan JSON Catalog

[← Kembali ke Guides](./README.md)

Panduan ini menerapkan [Decision 1.10](../decisions/1-error-definition-framework/1.10-generator-error-codes-typescript.md) dan [Decision 1.11](../decisions/1-error-definition-framework/1.11-generator-error-catalog-json.md).

## Mengatur Output Path

Default config cocok untuk frontend yang berada dalam project Laravel yang sama:

```php
// config/error-definition.php

return [
    'generation' => [
        'typescript_path' => resource_path(
            'js/generated/error-codes.ts',
        ),
        'catalog_path' => storage_path(
            'app/error-definition/error-catalog.json',
        ),
    ],
];
```

Untuk frontend repository yang terpisah, arahkan TypeScript path ke workspace terkait:

```php
'typescript_path' => base_path(
    '../frontend/src/generated/error-codes.ts',
),
```

## Menghasilkan Artifact

```shell
php artisan error-definition:generate
```

Contoh output:

```text
generated  resources/js/generated/error-codes.ts
unchanged  storage/app/error-definition/error-catalog.json
```

Command membuat parent directory dan hanya mengganti file yang content-nya berubah.

## Menggunakan TypeScript

```ts
import {
  ERROR_CODES,
  type ErrorCode,
} from './generated/error-codes';

function handleError(code: ErrorCode): void {
  if (code === ERROR_CODES.SM_WF_001) {
    // Tampilkan tindakan yang sesuai.
  }
}
```

Jangan menambahkan constant secara manual ke generated file. Tambahkan Error Definition pada PHP lalu jalankan generator kembali.

## Membaca JSON Catalog

Catalog memuat metadata runtime:

```json
{
  "schemaVersion": 1,
  "errors": [
    {
      "code": "SM-WF-001",
      "message": "Draft sedang dikunci.",
      "category": "workflow",
      "httpStatus": 409,
      "severity": "medium",
      "retryable": false
    }
  ]
}
```

Catalog lokal dapat digunakan tooling atau disiapkan sebagai artifact publish. Jangan menambahkan owner, SLA, atau Knowledge Base ke generated file karena enrichment tersebut dikelola oleh registry.

## Memeriksa Artifact pada CI

```shell
php artisan error-definition:generate --check
```

Contoh hasil stale:

```text
up to date  resources/js/generated/error-codes.ts
stale       storage/app/error-definition/error-catalog.json

1 stale artifact
```

Command mengembalikan exit code `1` tanpa mengubah working tree. Jalankan normal generation dan commit artifact apabila build workflow project menyimpan generated file dalam Git.
