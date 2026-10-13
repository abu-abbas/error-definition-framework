# Configuration Reference

[← Kembali ke Reference](./README.md)

Package menggunakan satu file konfigurasi: `config/error-definition.php`.

## `logging.*`

| Key | Tipe | Default | Ditetapkan pada |
|---|---|---|---|
| `additional_sensitive_keys` | `string[]` | `[]` | [Decision 1.7](../decisions/1-error-definition-framework/1.7-logging-dan-context.md) |

## `generation.*`

| Key | Tipe | Default | Ditetapkan pada |
|---|---|---|---|
| `typescript_path` | `string` | `resource_path('js/generated/error-codes.ts')` | [Decision 1.10](../decisions/1-error-definition-framework/1.10-generator-error-codes-typescript.md) |
| `catalog_path` | `string` | `storage_path('app/error-definition/error-catalog.json')` | [Decision 1.10](../decisions/1-error-definition-framework/1.10-generator-error-codes-typescript.md) |

## `publish.*`

| Key | Tipe | Default | Ditetapkan pada |
|---|---|---|---|
| `driver` | `string` | `env('EDF_PUBLISH_DRIVER', 'disk')` | [Decision 2.1](../decisions/2-publish-catalog/2.1-publish-catalog-dari-aplikasi.md) |
| `application` | `?string` | `env('EDF_PUBLISH_APPLICATION')` | [Decision 2.1](../decisions/2-publish-catalog/2.1-publish-catalog-dari-aplikasi.md) |
| `disk.name` | `string` | `env('EDF_PUBLISH_DISK', 'local')` | [Decision 2.1](../decisions/2-publish-catalog/2.1-publish-catalog-dari-aplikasi.md) |
| `disk.path` | `string` | `'error-definition/{application}.json'` | [Decision 2.1](../decisions/2-publish-catalog/2.1-publish-catalog-dari-aplikasi.md) |
| `http.url` | `?string` | `env('EDF_PUBLISH_URL')` | [Decision 2.1](../decisions/2-publish-catalog/2.1-publish-catalog-dari-aplikasi.md) |
| `http.token` | `?string` | `env('EDF_PUBLISH_TOKEN')` | [Decision 2.1](../decisions/2-publish-catalog/2.1-publish-catalog-dari-aplikasi.md) |
| `http.timeout` | `int` | `10` | [Decision 2.1](../decisions/2-publish-catalog/2.1-publish-catalog-dari-aplikasi.md) |
