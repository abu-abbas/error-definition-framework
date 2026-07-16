# ErrorDefinitionReader Reference

[← Kembali ke Reference](./README.md)

## Method

```php
public function read(
    ErrorCode&BackedEnum $error
): ResolvedErrorDefinition
```

Method menerima satu enum case yang mengimplementasikan `ErrorCode` dan merupakan `BackedEnum`.

```php
$definition = $reader->read(
    SuratMasukError::DRAFT_LOCKED
);
```

## Behavior

- membaca attribute `ErrorDefinition` menggunakan Reflection;
- mengembalikan `ResolvedErrorDefinition`;
- melempar `MissingErrorDefinitionException` jika attribute tidak ditemukan;
- menyimpan hasil resolve pada in-memory cache selama lifecycle Reader.
