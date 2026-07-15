# 1.1 ErrorDefinition

## Tujuan

`ErrorDefinition` adalah metadata standar yang melekat pada setiap error code. Metadata ini menjadi **single source of truth** bagi aplikasi untuk menentukan bagaimana sebuah error direpresentasikan, baik pada exception, validation, maupun proses generate catalog.

## Implementasi

Menggunakan **PHP Attribute** yang ditempelkan pada setiap enum case.

```php
#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
final readonly class ErrorDefinition
{
    public function __construct(
        public string $message,
        public ErrorCategory $category,
        public int $httpStatus,
        public ErrorSeverity $severity = ErrorSeverity::MEDIUM,
        public bool $retryable = false,
    ) {}
}
```

Contoh penggunaan:

```php
enum SuratMasukError: string
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

## Alasan menggunakan Attribute

* Metadata berada tepat di samping error code.
* Tidak terjadi duplikasi antara kode dan definisi.
* Mudah di-scan menggunakan Reflection.
* Mudah di-generate menjadi TypeScript dan JSON.
* Menjadi pondasi untuk validation, exception, registry, dan dokumentasi.

## Arti setiap field

| Field        | Keterangan                                                                          |
| ------------ | ----------------------------------------------------------------------------------- |
| `message`    | Pesan default yang aman ditampilkan kepada pengguna.                                |
| `category`   | Kategori error (Validation, Workflow, Business Rule, Integration, dan sebagainya).  |
| `httpStatus` | HTTP Status yang akan dikembalikan API. Wajib ditentukan agar semantik error jelas. |
| `severity`   | Tingkat dampak error untuk logging, monitoring, dan analisis.                       |
| `retryable`  | Menandakan apakah operasi layak dicoba kembali (retry).                             |

## Yang sengaja belum dimasukkan

Field berikut **bukan bagian dari ErrorDefinition** karena merupakan concern tahap berikutnya:

* Knowledge Base (KB)
* Owner
* SLA
* Escalation
* Registry metadata
* Module
* Error Code (sudah berasal dari value enum)

## Keputusan

* Menggunakan **PHP Attribute** sebagai media penyimpanan metadata.
* Menggunakan **Backed Enum** (`string`) sebagai error code.
* `httpStatus` wajib diisi (tidak memiliki default).
* `message` menjadi default message untuk exception maupun validation.
* `ErrorDefinition` hanya menyimpan metadata yang dibutuhkan oleh aplikasi pada saat runtime.
