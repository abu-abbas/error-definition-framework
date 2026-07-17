# Menemukan Error Definition

[← Kembali ke Guides](./README.md)

Panduan ini menerapkan [Decision 1.9](../decisions/1-error-definition-framework/1.9-error-definition-discovery-mechanism.md).

## Composer Autoload

Letakkan source code pada path yang sudah dideklarasikan dalam root `composer.json`:

```json
{
  "autoload": {
    "psr-4": {
      "App\\": "app/",
      "Modules\\": "modules/"
    },
    "classmap": [
      "legacy/"
    ]
  },
  "autoload-dev": {
    "psr-4": {
      "Tests\\": "tests/"
    }
  }
}
```

Discovery memeriksa `app/`, `modules/`, dan `legacy/`. Folder `tests/` serta dependency di dalam `vendor/` tidak diperiksa.

Tidak ada kewajiban menggunakan folder `Errors` atau `Http/Requests`. Target dikenali dari kontrak tipe:

```php
enum SuratMasukError: string implements ErrorCode
{
    // ...
}
```

```php
final class StoreSuratMasukRequest extends FormRequest
{
    use HasErrorDefinitions;

    // ...
}
```

## Memperbarui Autoloader

Jalankan Composer setelah menambahkan atau mengubah autoload mapping:

```shell
composer dump-autoload
```

Discovery membaca `composer.json`, tetapi class tetap dimuat melalui Composer autoloader yang telah dibuat.

## Menggunakan Hasil Discovery

```php
$targets = $discovery->discover();

$report = $linter->lint(
    errorEnums: $targets->errorEnums,
    formRequests: $targets->formRequests,
);
```

Pemanggilan `discover()` berikutnya pada process yang sama menggunakan in-memory snapshot.

## Target yang Tidak Ditemukan

Periksa hal berikut apabila class tidak masuk hasil discovery:

- file berada pada root application autoload, bukan `autoload-dev` atau `vendor/`;
- namespace dan path mengikuti mapping PSR-4;
- enum mengimplementasikan `ErrorCode`;
- FormRequest menggunakan `HasErrorDefinitions`;
- FormRequest tidak abstract;
- Composer autoloader sudah diperbarui.

Jangan menambahkan daftar class atau scan path manual. Perbaiki Composer autoload atau kontrak tipe yang belum sesuai.

## Batas Dependency Package

Error Definition dari dependency di dalam `vendor/` belum ditemukan otomatis. Berikan target secara eksplisit kepada consumer jika integrasi sementara diperlukan. Extension mechanism khusus package akan ditambahkan ketika terdapat use case nyata.
