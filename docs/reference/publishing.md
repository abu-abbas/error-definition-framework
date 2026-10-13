# Publishing Reference

[← Kembali ke Reference](./README.md)

## Artisan Command

```shell
php artisan error-definition:publish --revision=<revision>
```

| Exit code | Arti |
|---|---|
| `0` | Catalog berhasil ditulis atau diterima penerima. |
| `1` | Config tidak valid, `--revision` kosong, lint error, discovery gagal, atau driver gagal mengirim. |

Urutan pemeriksaan: identitas aplikasi dan revision, driver, discovery dan linter, lalu pengiriman.

## PublishPayload

`Edf\Core\Publishing\PublishPayload`

```php
public static function create(mixed $application, mixed $revision, array $catalog): self

public static function assertIdentity(mixed $application, mixed $revision): void

public function toArray(): array

public function toJson(): string
```

| Konstanta | Nilai |
|---|---|
| `APPLICATION_FORMAT` | `/^[a-z0-9]+(?:-[a-z0-9]+)*$/` |
| `MAX_REVISION_LENGTH` | `64` |

`create()` dan `assertIdentity()` melempar `Edf\Core\Publishing\PublishException` jika identitas aplikasi bukan slug atau revision kosong atau lebih dari 64 karakter. `toJson()` memakai serialization yang sama dengan `error-catalog.json`.

## CatalogPublisher

`Edf\Laravel\Publishing\CatalogPublisher`

```php
/** @return string tujuan yang ditampilkan pada output command */
public function publish(PublishPayload $payload): string;
```

| Driver | Class | Tujuan |
|---|---|---|
| `disk` | `DiskPublisher` | `{disk}:{path}` pada filesystem disk Laravel |
| `http` | `HttpPublisher` | `POST` ke `publish.http.url` |

Kegagalan dilaporkan dengan `PublishException`.

## JSON Schema

| File | Isi |
|---|---|
| `resources/schemas/publish-request.v1.json` | Envelope `application`, `revision`, `catalog` |
| `resources/schemas/error-catalog.v1.json` | Catalog `schemaVersion: 1` |

Schema mengikuti JSON Schema draft 2020-12 dan mengizinkan field tambahan. Keunikan `code` dalam satu catalog tidak dinyatakan pada schema.

Config tercatat pada [Configuration Reference](./configuration.md#publish).
