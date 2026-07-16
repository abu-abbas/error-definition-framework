# ResolvedErrorDefinition Reference

[← Kembali ke Reference](./README.md)

`ResolvedErrorDefinition` adalah DTO hasil penggabungan error code dari enum dengan metadata dari `ErrorDefinition`.

```php
final readonly class ResolvedErrorDefinition
{
    public function __construct(
        public string $code,
        public string $message,
        public ErrorCategory $category,
        public int $httpStatus,
        public ErrorSeverity $severity,
        public bool $retryable,
    ) {}
}
```

DTO ini tidak menyimpan enum source maupun object Reflection sehingga dapat langsung digunakan oleh exception, validation, response, logging, dan generator.
