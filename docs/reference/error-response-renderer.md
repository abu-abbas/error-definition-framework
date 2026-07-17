# ErrorResponseRenderer Reference

[← Kembali ke Reference](./README.md)

`ErrorResponseRenderer` membentuk response JSON untuk exception yang membawa Error Definition.

## Methods

```php
public function application(
    ApplicationException $exception,
): JsonResponse
```

```php
public function validation(
    ErrorValidationException $exception,
): JsonResponse
```

Laravel Service Provider mendaftarkan kedua method tersebut secara otomatis pada exception handler. Delegasi hanya dilakukan untuk request yang dianggap mengharapkan JSON oleh Laravel.

## Application Response

```json
{
  "message": "Draft sedang dikunci.",
  "code": "SM-WF-001",
  "retryable": false
}
```

- HTTP status berasal dari `definition.httpStatus`;
- `message`, `code`, dan `retryable` berasal dari `ResolvedErrorDefinition`;
- category, severity, runtime context, dan debug information tidak disertakan.

## Validation Response

```json
{
  "message": "Nomor surat wajib diisi.",
  "errors": {
    "nomor_surat": [
      {
        "code": "SM-VAL-001",
        "message": "Nomor surat wajib diisi.",
        "retryable": false
      }
    ]
  }
}
```

- HTTP status selalu `422`;
- top-level `message` berasal dari `ValidationException::getMessage()`;
- errors dikelompokkan menggunakan attribute aktual;
- setiap item berasal dari satu `ResolvedValidationError`;
- rule identifier tidak disertakan;
- urutan failure dari Laravel dipertahankan di dalam setiap field.

## Scope

Renderer hanya menangani `ApplicationException` dan `ErrorValidationException`. Request non-JSON dan exception lain tetap menggunakan exception handling Laravel.
