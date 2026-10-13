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

## `registry.*`

| Key | Tipe | Default | Ditetapkan pada |
|---|---|---|---|
| `url` | `?string` | `env('EDF_REGISTRY_URL')` | [Decision 2.1](../decisions/2-central-error-registry/2.1-publish-catalog-dari-aplikasi.md) |
| `token` | `?string` | `env('EDF_REGISTRY_TOKEN')` | [Decision 2.1](../decisions/2-central-error-registry/2.1-publish-catalog-dari-aplikasi.md) |
| `application` | `?string` | `env('EDF_REGISTRY_APPLICATION')` | [Decision 2.1](../decisions/2-central-error-registry/2.1-publish-catalog-dari-aplikasi.md) |
| `timeout` | `int` | `10` | [Decision 2.1](../decisions/2-central-error-registry/2.1-publish-catalog-dari-aplikasi.md) |
