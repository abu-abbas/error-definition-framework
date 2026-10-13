---
name: error-definition-development
description: "Use when adding, changing, throwing, or validating application errors in a Laravel app that uses abu-abbas/error-definition-framework (EDF). Trigger when the task touches enums implementing Edf\\Core\\ErrorCode, the #[ErrorDefinition] attribute, ApplicationException, FormRequests using HasErrorDefinitions, the error-definition:lint or error-definition:generate commands, generated error-codes.ts or error-catalog.json, or error codes like SM-WF-001. Do not trigger for generic Laravel exception handling in apps without this package."
license: MIT
metadata:
  author: abu-abbas
---
# Error Definition Framework

Errors are defined once in PHP and everything else (HTTP responses, validation errors, logs, frontend TypeScript, JSON catalog) is derived from that definition. Never duplicate a code or message outside the enum.

## Define an Error

Each module owns one string-backed enum that implements `ErrorCode`. Each case carries exactly one `#[ErrorDefinition]`.

```php
use Edf\Core\Attributes\ErrorDefinition;
use Edf\Core\ErrorCategory;
use Edf\Core\ErrorCode;
use Edf\Core\ErrorSeverity;

enum SuratMasukError: string implements ErrorCode
{
    #[ErrorDefinition(
        message: 'Surat sudah disetujui dan tidak dapat diubah.',
        category: ErrorCategory::WORKFLOW,
        httpStatus: 409,
        severity: ErrorSeverity::MEDIUM,
    )]
    case DRAFT_LOCKED = 'SM-WF-001';
}
```

Rules the linter enforces:

- The code matches `^[A-Z0-9]+(?:-[A-Z0-9]+)+-\d{3,}$`, for example `SM-WF-001` or `USER-VAL-002`, and is unique across the whole app.
- `message` is not empty. `httpStatus` is between 400 and 599.
- `severity` defaults to `MEDIUM` and `retryable` defaults to `false`.
- Categories: `VALIDATION`, `AUTHENTICATION`, `AUTHORIZATION`, `NOT_FOUND`, `BUSINESS_RULE`, `WORKFLOW`, `INTEGRATION`, `SYSTEM`.
- Severities: `LOW`, `MEDIUM`, `HIGH`, `CRITICAL`.

Put the enum in the module that owns the concept (a "role already assigned to user" error belongs to `UserRoleAssignmentError`, not `RoleError`). Any folder works as long as it is under the app's `autoload` (not `autoload-dev`) in `composer.json`. Run `composer dump-autoload` after adding a new path.

Never reuse or renumber an existing code: clients and logs depend on it. Add a new case with the next number instead.

## Throw an Error

```php
use Edf\Core\Exceptions\ApplicationException;

throw ApplicationException::of(
    SuratMasukError::DRAFT_LOCKED,
    context: [
        'user_id' => $user->id,
        'document_id' => $surat->id,
    ],
);
```

- For JSON requests the package renders `{"message", "code", "retryable"}` with the definition's HTTP status. Do not build these responses by hand or use `abort()` for defined errors.
- `context` goes to logs only, never to the response. Pass scalar identifiers, not models, requests, or payloads.
- Sensitive keys (passwords, tokens, plus `logging.additional_sensitive_keys` in `config/error-definition.php`) are redacted automatically. Add domain-specific keys there instead of filtering by hand.
- Never put personal data or secrets in `message`; only `context` is sanitized.

## Validation Errors

FormRequests opt in with the `HasErrorDefinitions` trait. Every rule must map to an error code, and the request must not define `messages()`.

```php
use Edf\Laravel\Validation\HasErrorDefinitions;

final class StoreSuratMasukRequest extends FormRequest
{
    use HasErrorDefinitions;

    public function rules(): array
    {
        return [
            'nomor_surat' => ['required'],
            'attachments.*.file' => ['required', 'file'],
            'name' => [new Uppercase],
        ];
    }

    public function errorCodes(): array
    {
        return [
            'nomor_surat.required' => SuratMasukError::NOMOR_SURAT_REQUIRED,
            'attachments.*.file.required' => SuratMasukError::ATTACHMENT_REQUIRED,
            'attachments.*.file.file' => SuratMasukError::ATTACHMENT_INVALID,
            'name.'.Uppercase::class => SuratMasukError::NAME_MUST_BE_UPPERCASE,
        ];
    }
}
```

- Keys are `attribute.rule`. Use the snake_case rule name for string rules and the class name for rule objects. Wildcard attributes are supported.
- Errors used for validation must have `category: ErrorCategory::VALIDATION`, `httpStatus: 422`, and `retryable: false`. Laravel placeholders such as `:attribute` work in the message.
- Use `attributes()` for human-readable field names.
- For checks in `after()`, call `$this->addValidationError(validator: $validator, attribute: 'schedule', rule: 'available', error: SuratMasukError::SCHEDULE_UNAVAILABLE)`.

## Verify

Run both commands after every change to error enums or opted-in FormRequests:

```shell
php artisan error-definition:lint
php artisan error-definition:generate
```

- `lint` exits with 1 on errors (`--strict` also fails on warnings). Fix every `EDFxxx` and `VALxxx` finding; do not silence them.
- `generate` writes `error-codes.ts` (default `resources/js/generated/`) and `error-catalog.json`. Never edit these files by hand. Projects that commit them can check freshness with `php artisan error-definition:generate --check`.
- `error-definition:publish --revision=<sha>` sends the full catalog to the destination in `config('error-definition.publish')` (`disk` or `http`). It belongs in a CI step separate from deployment; do not call it from application code. It needs `EDF_PUBLISH_APPLICATION` set to a lowercase slug.

## Frontend

Import codes from the generated file instead of string literals:

```ts
import { ERROR_CODES, type ErrorCode } from './generated/error-codes';

if (code === ERROR_CODES.SM_WF_001) {
  // ...
}
```
