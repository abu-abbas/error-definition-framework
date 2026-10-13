# Error Definition Framework

[![Latest Version](https://img.shields.io/packagist/v/abu-abbas/error-definition-framework)](https://packagist.org/packages/abu-abbas/error-definition-framework)
[![License](https://img.shields.io/packagist/l/abu-abbas/error-definition-framework)](./LICENSE)

Laravel package untuk mendefinisikan error aplikasi satu kali, di PHP enum, lalu memakai definisi yang sama untuk response API, validation, logging, dan frontend.

```php
enum SuratMasukError: string implements ErrorCode
{
    #[ErrorDefinition(
        message: 'Surat sudah disetujui dan tidak dapat diubah.',
        category: ErrorCategory::WORKFLOW,
        httpStatus: 409,
    )]
    case DRAFT_LOCKED = 'SM-WF-001';
}

throw ApplicationException::of(SuratMasukError::DRAFT_LOCKED, context: ['document_id' => $surat->id]);
```

```json
HTTP/1.1 409 Conflict

{ "message": "Surat sudah disetujui dan tidak dapat diubah.", "code": "SM-WF-001", "retryable": false }
```

## Fitur

- **Satu sumber kebenaran**: kode, message, category, HTTP status, severity, dan retryable didefinisikan di enum case melalui attribute.
- **Response JSON konsisten** untuk `ApplicationException`, otomatis lewat exception handler Laravel.
- **Validation dengan kode error per field**: FormRequest memetakan setiap rule ke error code, dan response 422 membawa kode tersebut.
- **Structured logging**: level log mengikuti severity, dan data sensitif di context otomatis disensor.
- **Linter** (`error-definition:lint`) untuk menangkap kode duplikat, format salah, atau rule validation yang belum dipetakan.
- **Generator** (`error-definition:generate`) untuk `error-codes.ts` di frontend dan `error-catalog.json`.
- **Agent skill** untuk Claude dan AI coding agent lain, supaya agent menulis error mengikuti framework ini.

## Kebutuhan

- PHP 8.3 atau lebih baru
- Laravel 13

## Instalasi

```bash
composer require abu-abbas/error-definition-framework
```

Service provider terdaftar otomatis melalui Laravel package discovery. Publish config jika perlu mengubah output path generator atau menambah sensitive key:

```bash
php artisan vendor:publish --tag=error-definition-config
```

## Penggunaan

### 1. Definisikan error

Buat enum per modul yang mengimplementasikan `ErrorCode`, di folder mana pun yang termasuk Composer autoload aplikasi.

```php
use Edf\Core\Attributes\ErrorDefinition;
use Edf\Core\ErrorCategory;
use Edf\Core\ErrorCode;
use Edf\Core\ErrorSeverity;

enum SuratMasukError: string implements ErrorCode
{
    #[ErrorDefinition(
        message: 'Nomor surat wajib diisi.',
        category: ErrorCategory::VALIDATION,
        httpStatus: 422,
        severity: ErrorSeverity::LOW,
    )]
    case NOMOR_SURAT_REQUIRED = 'SM-VAL-001';

    #[ErrorDefinition(
        message: 'Surat sudah disetujui dan tidak dapat diubah.',
        category: ErrorCategory::WORKFLOW,
        httpStatus: 409,
    )]
    case DRAFT_LOCKED = 'SM-WF-001';
}
```

### 2. Lempar error

```php
use Edf\Core\Exceptions\ApplicationException;

throw ApplicationException::of(
    SuratMasukError::DRAFT_LOCKED,
    context: ['user_id' => $user->id, 'document_id' => $surat->id],
);
```

Request JSON menerima response berisi `message`, `code`, dan `retryable` dengan HTTP status dari definisi. Context hanya masuk ke log.

### 3. Petakan validation

```php
use Edf\Laravel\Validation\HasErrorDefinitions;

final class StoreSuratMasukRequest extends FormRequest
{
    use HasErrorDefinitions;

    public function rules(): array
    {
        return ['nomor_surat' => ['required']];
    }

    public function errorCodes(): array
    {
        return ['nomor_surat.required' => SuratMasukError::NOMOR_SURAT_REQUIRED];
    }
}
```

```json
{
  "message": "Nomor surat wajib diisi.",
  "errors": {
    "nomor_surat": [
      { "code": "SM-VAL-001", "message": "Nomor surat wajib diisi.", "retryable": false }
    ]
  }
}
```

### 4. Periksa dan generate

```bash
php artisan error-definition:lint
php artisan error-definition:generate
```

Frontend memakai kode dari file hasil generate, bukan string yang ditulis ulang:

```ts
import { ERROR_CODES, type ErrorCode } from './generated/error-codes';

if (code === ERROR_CODES.SM_WF_001) {
  // ...
}
```

## Agent Skill

Package menyertakan Agent Skill `error-definition-development` agar AI coding agent seperti Claude menulis error mengikuti framework ini, bukan `abort()` atau message bebas.

**Claude Code**: salin skill ke project.

```bash
mkdir -p .claude/skills
cp -r vendor/abu-abbas/error-definition-framework/resources/boost/skills/error-definition-development .claude/skills/
```

**Laravel Boost**: jalankan `php artisan boost:install`, lalu pilih `abu-abbas/error-definition-framework` di daftar third-party guidelines/skills.

Cara pasang di Claude.ai, Claude Desktop, dan agent lain ada di [panduan Agent Skill](./docs/guides/agent-skills.md).

## Dokumentasi

Mulai dari [documentation index](./docs/README.md) untuk filosofi framework dan peta seluruh dokumentasi.

- [Guides](./docs/guides/): cara memakai setiap fitur
- [API reference](./docs/reference/): class, method, dan kontrak
- [Concepts](./docs/concepts/): istilah dan konsep dasar
- [Design decisions](./docs/decisions/): alasan di balik desainnya
- [Roadmap](./docs/roadmap.md)

## Kontribusi

Lihat [CONTRIBUTING.md](./CONTRIBUTING.md).

## License

MIT. Lihat [LICENSE](./LICENSE).
