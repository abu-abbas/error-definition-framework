# ApplicationException Reference

[← Kembali ke Reference](./README.md)

```php
final class ApplicationException extends RuntimeException
{
    /**
     * @param array<string, mixed> $context
     */
    public function __construct(
        public readonly ResolvedErrorDefinition $definition,
        public readonly array $context = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            message: $definition->message,
            code: 0,
            previous: $previous,
        );
    }
}
```

## Static Factory

```php
/** @param array<string, mixed> $context */
public static function of(
    ErrorCode&BackedEnum $error,
    array $context = [],
    ?Throwable $previous = null,
): self
```

Resolve enum case melalui `ErrorDefinitionReader` lalu membuat exception. Enum case tanpa `ErrorDefinition` menghasilkan `MissingErrorDefinitionException`.

```php
public static function useReader(ErrorDefinitionReader $reader): void
```

Menentukan Reader yang dipakai `of()`. Laravel adapter memanggilnya dengan Reader singleton aplikasi; tanpa adapter, `of()` membuat Reader sendiri.

## Properties

| Property | Type | Keterangan |
|----------|------|------------|
| `definition` | `ResolvedErrorDefinition` | Error Definition yang telah di-resolve. |
| `context` | `array<string, mixed>` | Data internal untuk logging dan debugging. |

## Behavior

- message berasal dari `definition->message`;
- exception code bawaan bernilai `0`;
- string error code tersedia melalui `definition->code`;
- previous exception diteruskan ke parent;
- context tidak otomatis masuk response dan tidak disanitasi oleh exception.
