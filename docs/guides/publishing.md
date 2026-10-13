# Publish Catalog

[← Kembali ke Guides](./README.md)

Panduan ini menerapkan [Decision 2.1](../decisions/2-publish-catalog/2.1-publish-catalog-dari-aplikasi.md) dan [Decision 2.2](../decisions/2-publish-catalog/2.2-validasi-schema.md).

`error-definition:publish` mengirim catalog lengkap aplikasi ke luar aplikasi, misalnya ke storage bersama, server lain lewat SFTP, atau endpoint HTTP milik tim.

## Mengatur Identitas Aplikasi

Setiap aplikasi membutuhkan identitas tetap berupa slug huruf kecil:

```dotenv
EDF_PUBLISH_APPLICATION=surat-masuk
```

Jangan mengganti identitas setelah aplikasi pernah publish, karena penerima akan menganggapnya aplikasi baru.

## Menjalankan Publish

```shell
php artisan error-definition:publish --revision="$(git rev-parse HEAD)"
```

Contoh output:

```text
published  local:error-definition/surat-masuk.json (12 errors, revision 4f9c2e1...)
```

Command menjalankan discovery dan linter yang sama dengan `error-definition:generate`. Lint error menghentikan publish sebelum apa pun dikirim.

## Driver `disk`

Driver default. Catalog ditulis ke filesystem disk Laravel dan ditimpa setiap publish.

```dotenv
EDF_PUBLISH_DRIVER=disk
EDF_PUBLISH_DISK=local
```

Dengan disk `local` bawaan Laravel 13, file berada di `storage/app/private/error-definition/surat-masuk.json`. Path dalam disk dapat diubah pada config `publish.disk.path`.

### Menyalin ke Server Lain

Gunakan disk `sftp` agar catalog langsung ditulis ke server tujuan:

```php
// config/filesystems.php

'disks' => [
    'error-catalogs' => [
        'driver' => 'sftp',
        'host' => env('EDF_SFTP_HOST'),
        'username' => env('EDF_SFTP_USERNAME'),
        'privateKey' => env('EDF_SFTP_PRIVATE_KEY'),
        'root' => '/srv/error-catalogs',
    ],
],
```

```dotenv
EDF_PUBLISH_DISK=error-catalogs
```

Disk `sftp` membutuhkan `league/flysystem-sftp-v3`, dan disk `s3` membutuhkan `league/flysystem-aws-s3-v3`. Pasang adapter tersebut di aplikasi; package tidak memasangnya.

Jika tim lebih nyaman dengan `rsync`, publish ke disk `local` lalu jalankan `rsync` sebagai langkah CI berikutnya.

## Driver `http`

Catalog dikirim dengan `POST` ke URL yang dikonfigurasi:

```dotenv
EDF_PUBLISH_DRIVER=http
EDF_PUBLISH_URL=https://catalog.internal.example/api/catalogs
EDF_PUBLISH_TOKEN=token-rahasia-dari-secret-ci
```

Jika `EDF_PUBLISH_TOKEN` diisi, command mengirim header `Authorization: Bearer <token>`. Respons `2xx` dianggap berhasil. Status lain, timeout, atau network error membuat command gagal dan menampilkan status serta field `message` dari body jika ada.

Command tidak melakukan retry. Jalankan ulang langkah CI jika penerima sedang tidak tersedia.

## Isi Kiriman

Semua driver mengirim JSON yang sama:

```json
{
  "application": "surat-masuk",
  "revision": "4f9c2e1",
  "catalog": {
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
}
```

Penerima dapat memvalidasi kiriman dengan JSON Schema yang dibawa package:

- `vendor/abu-abbas/error-definition-framework/resources/schemas/publish-request.v1.json`
- `vendor/abu-abbas/error-definition-framework/resources/schemas/error-catalog.v1.json`

## Menjalankan pada CI

Jalankan publish sebagai langkah terpisah dari deployment, sehingga kegagalan publish tidak membatalkan deployment:

```yaml
- name: Publish error catalog
  continue-on-error: true
  run: php artisan error-definition:publish --revision="${{ github.sha }}"
  env:
    EDF_PUBLISH_APPLICATION: surat-masuk
    EDF_PUBLISH_DRIVER: http
    EDF_PUBLISH_URL: ${{ vars.EDF_PUBLISH_URL }}
    EDF_PUBLISH_TOKEN: ${{ secrets.EDF_PUBLISH_TOKEN }}
```

Simpan token dan credential SFTP sebagai secret CI, bukan di file yang di-commit.
