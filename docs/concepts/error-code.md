# Error Code

Error code adalah identifier typed yang merepresentasikan kondisi error secara unik.

Error code menggunakan PHP Backed Enum dan didefinisikan satu kali pada enum case.

```php
enum SuratMasukError: string implements ErrorCode
{
    case DRAFT_LOCKED = 'SM-WF-001';
}
```

## Format

```text
SM-WF-001
│  │   └── nomor urut
│  └────── kategori atau konteks error
└───────── prefix modul atau fitur
```

Contoh prefix:

```text
SM   = Surat Masuk
USR  = User
ROL  = Role
URA  = User Role Assignment
```

Nomor yang telah digunakan tidak boleh digunakan ulang. Prefix harus konsisten dalam satu konteks dan tidak boleh ambigu.
