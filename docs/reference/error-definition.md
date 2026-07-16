# ErrorDefinition Reference

[← Kembali ke Reference](./README.md)

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

Attribute ditempelkan pada enum case.

```php
#[ErrorDefinition(
    message: 'Draft sedang dikunci.',
    category: ErrorCategory::WORKFLOW,
    httpStatus: 409,
    severity: ErrorSeverity::MEDIUM,
)]
case DRAFT_LOCKED = 'SM-WF-001';
```
