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
