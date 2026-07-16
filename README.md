# Error Definition Framework

Framework untuk mendefinisikan, mengelola, dan membagikan error secara terstruktur dan konsisten.

## Filosofi

- Laravel merupakan source of truth.
- Setiap error didefinisikan satu kali.
- Error code menggunakan PHP Backed Enum.
- Metadata error menggunakan PHP Attribute.
- Frontend menggunakan hasil generate, bukan mendefinisikan ulang error.
- Runtime aplikasi tidak bergantung pada Central Error Registry.

## Quick Example

```php
enum SuratMasukError: string implements ErrorCode
{
    #[ErrorDefinition(
        message: 'Draft sedang dikunci.',
        category: ErrorCategory::WORKFLOW,
        httpStatus: 409,
        severity: ErrorSeverity::MEDIUM,
    )]
    case DRAFT_LOCKED = 'SM-WF-001';
}
```

## Documentation

Mulai dari [documentation index](./docs/README.md).

- [Design decisions](./docs/decisions/)
- [Roadmap](./docs/roadmap.md)

Concepts, API reference, dan guides akan ditambahkan bersama implementasinya.
