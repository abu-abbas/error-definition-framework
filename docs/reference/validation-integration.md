# Validation Integration Reference

[← Kembali ke Reference](./README.md)

## HasErrorDefinitions

```php
trait HasErrorDefinitions
{
    /**
     * @return array<string, ErrorCode&BackedEnum>
     */
    abstract public function errorCodes(): array;

    protected function addValidationError(
        Validator $validator,
        string $attribute,
        string $rule,
        ErrorCode&BackedEnum $error,
    ): void;
}
```

Mapping menggunakan key `attribute.rule`. Nested attribute mendukung dot notation dan wildcard.

## ResolvedValidationError

```php
final readonly class ResolvedValidationError
{
    public function __construct(
        public string $attribute,
        public string $rule,
        public ResolvedErrorDefinition $definition,
        public string $message,
    ) {}
}
```

Property `message` berisi message akhir setelah placeholder Laravel diproses.

## ErrorValidationException

```php
final class ErrorValidationException extends ValidationException
{
    /**
     * @param list<ResolvedValidationError> $resolvedErrors
     */
    public function __construct(
        Validator $validator,
        public readonly array $resolvedErrors,
    ) {
        parent::__construct($validator);
    }
}
```

Exception mempertahankan validator, redirect, named error bag, dan behavior JSON Laravel.

## Configuration Exceptions

- `MissingValidationErrorCodeException` dilempar ketika rule yang gagal tidak memiliki mapping.
- `InvalidValidationErrorDefinitionException` dilempar ketika definition bukan category `VALIDATION`, status bukan `422`, atau `retryable` bukan `false`.
