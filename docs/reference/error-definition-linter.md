# ErrorDefinitionLinter Reference

[← Kembali ke Reference](./README.md)

## ErrorDefinitionLinter

```php
/**
 * @param iterable<class-string<ErrorCode&BackedEnum>> $errorEnums
 * @param iterable<class-string<FormRequest>> $formRequests
 */
public function lint(
    iterable $errorEnums,
    iterable $formRequests = [],
): LintReport
```

Linter mengaudit seluruh target dan mengembalikan satu report. Method tidak melakukan discovery dan tidak berhenti pada temuan pertama.

## LintLevel

```php
enum LintLevel: string
{
    case ERROR = 'error';
    case WARNING = 'warning';
}
```

## LintFinding

```php
final readonly class LintFinding
{
    public function __construct(
        public string $ruleId,
        public LintLevel $level,
        public string $source,
        public string $message,
    ) {}
}
```

## LintReport

```php
final readonly class LintReport
{
    /** @param list<LintFinding> $findings */
    public function __construct(
        public array $findings,
    ) {}
}
```

```php
public function hasErrors(): bool
```

```php
public function hasWarnings(): bool
```

Consumer dapat memeriksa public property `findings` dan memfilternya berdasarkan `LintLevel` tanpa mengubah urutan temuan.

## Error Definition Rules

| Rule ID | Level | Kondisi |
|---------|-------|---------|
| `EDF001` | Error | Target bukan enum `ErrorCode` dan `BackedEnum`. |
| `EDF002` | Error | Enum tidak menggunakan string backing. |
| `EDF003` | Error | Enum case tidak memiliki `ErrorDefinition`. |
| `EDF004` | Error | Enum case memiliki lebih dari satu `ErrorDefinition`. |
| `EDF005` | Error | Error code tidak mengikuti format framework. |
| `EDF006` | Error | Error code digunakan oleh lebih dari satu enum case. |
| `EDF007` | Error | Message kosong atau hanya berisi whitespace. |
| `EDF008` | Error | HTTP status berada di luar rentang `400–599`. |

Format error code:

```regex
^[A-Z0-9]+(?:-[A-Z0-9]+)+-\d{3,}$
```

## Validation Rules

| Rule ID | Level | Kondisi |
|---------|-------|---------|
| `VAL001` | Error | Target bukan FormRequest yang menggunakan `HasErrorDefinitions`. |
| `VAL002` | Warning | `rules()` atau `errorCodes()` tidak dapat dievaluasi. |
| `VAL003` | Error | Mapping key tidak mengikuti bentuk `attribute.rule`. |
| `VAL004` | Error | Mapping value bukan enum `ErrorCode` yang valid. |
| `VAL005` | Error | Rule yang dapat diidentifikasi tidak memiliki mapping. |
| `VAL006` | Warning | Mapping tidak memiliki pasangan rule yang terdeteksi. |
| `VAL007` | Warning | Dynamic rule tidak dapat diaudit secara pasti. |
| `VAL008` | Error | Definition mapping bukan category `VALIDATION`, status `422`, atau `retryable: false`. |
| `VAL009` | Error | FormRequest yang opt-in meng-override `messages()`. |

## Artisan Command

```shell
php artisan error-definition:lint [--strict]
```

| Kondisi | Exit Code |
|---------|-----------|
| Tidak ada error | `0` |
| Terdapat error | `1` |
| Hanya warning dengan `--strict` | `1` |

Command menampilkan finding berdasarkan level, rule ID, source, dan message, kemudian menampilkan jumlah error serta warning.

Command memperoleh target aplikasi dari discovery mechanism yang ditetapkan pada Stage 1.9. API `lint()` tetap dapat digunakan secara langsung dengan target eksplisit.
