# Mengorganisasikan Enum Error

[← Kembali ke Guides](./README.md)

Panduan ini menunjukkan cara menerapkan [Decision 1.2](../decisions/1-error-definition-framework/1.2-struktur-enum-error-per-modul.md).

## Contoh Enum Modul

```php
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
        severity: ErrorSeverity::MEDIUM,
    )]
    case DRAFT_LOCKED = 'SM-WF-001';
}
```

## Struktur Minimalis

Sesuai untuk aplikasi sederhana atau tim yang belum menggunakan struktur modular.

```text
app/
└── Errors/
    ├── SuratMasukError.php
    ├── UserError.php
    ├── RoleError.php
    └── UserRoleAssignmentError.php
```

## Struktur Modular

Sesuai untuk aplikasi yang mengelompokkan kode berdasarkan modul.

```text
app/
└── Modules/
    ├── SuratMasuk/
    │   └── Errors/
    │       └── SuratMasukError.php
    ├── User/
    │   └── Errors/
    │       ├── UserError.php
    │       └── UserRoleAssignmentError.php
    └── Role/
        └── Errors/
            └── RoleError.php
```

## Struktur Berbasis Domain

Sesuai untuk aplikasi dengan aturan bisnis kompleks. Struktur ini tetap dapat digunakan pada Laravel monolith dan tidak mensyaratkan microservices.

```text
app/
└── Domains/
    └── IdentityAccess/
        ├── Users/Errors/UserError.php
        ├── Roles/Errors/RoleError.php
        └── Assignments/Errors/UserRoleAssignmentError.php
```

Package tidak boleh bergantung pada salah satu struktur tersebut. Enum dapat didaftarkan melalui konfigurasi atau ditemukan melalui mekanisme discovery yang ditetapkan kemudian.

## Contoh Ownership

| Kondisi | Enum yang sesuai |
|---------|------------------|
| Email user sudah digunakan | `UserError` |
| User tidak ditemukan | `UserError` |
| Role tidak ditemukan | `RoleError` |
| Role sudah dimiliki user | `UserRoleAssignmentError` |
| Role tidak dapat diberikan kepada user tertentu | `UserRoleAssignmentError` |
