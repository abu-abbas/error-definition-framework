# Menjalankan Error Definition Linter

[← Kembali ke Guides](./README.md)

Panduan ini menerapkan [Decision 1.8](../decisions/1-error-definition-framework/1.8-validator-dan-linter.md).

## Menjalankan Pemeriksaan

Discovery mechanism menyediakan seluruh target aplikasi ketika pemeriksaan dijalankan melalui Artisan:

```shell
php artisan error-definition:lint
```

Contoh output:

```text
ERROR   EDF003  App\Errors\SuratMasukError::DRAFT_LOCKED
        ErrorDefinition attribute tidak ditemukan.

ERROR   EDF006  App\Errors\UserError::EMAIL_USED
        Duplicate error code USR-BR-001.

WARNING VAL007  App\Http\Requests\StoreUserRequest
        Closure validation rule tidak dapat diaudit secara penuh.

2 errors, 1 warning
```

## Strict Mode

Secara default, warning tidak menggagalkan command. Gunakan strict mode jika project telah menyelesaikan seluruh warning:

```shell
php artisan error-definition:lint --strict
```

## Penggunaan Langsung

Engine dapat digunakan sebelum discovery tersedia dengan target eksplisit:

```php
$report = $linter->lint(
    errorEnums: [
        SuratMasukError::class,
        UserError::class,
    ],
    formRequests: [
        StoreSuratMasukRequest::class,
        StoreUserRequest::class,
    ],
);

if ($report->hasErrors()) {
    // Gagalkan test atau tooling internal.
}
```

Daftar eksplisit ini hanya untuk test atau pemanggilan engine secara langsung. Jangan memeliharanya sebagai registry aplikasi; [Decision 1.9](../decisions/1-error-definition-framework/1.9-error-definition-discovery-mechanism.md) menyediakan target otomatis.

## CI

Tambahkan command ke pipeline CI setelah dependency terpasang dan aplikasi dapat di-bootstrap:

```shell
php artisan error-definition:lint --strict
```

Command mengembalikan exit code `1` ketika terdapat error atau warning dalam strict mode.

## Batas Audit Validation

Linter membandingkan mapping dengan rule yang dikembalikan ketika FormRequest dievaluasi. Conditional branch yang tidak aktif pada proses tersebut tetap diperiksa oleh strict runtime mapping ketika rule benar-benar gagal.

Perbaiki definite error terlebih dahulu. Warning menunjukkan bagian dinamis yang perlu ditinjau, bukan bukti bahwa mapping pasti salah.
