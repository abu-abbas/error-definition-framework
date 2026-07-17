# ErrorDefinitionDiscovery Reference

[← Kembali ke Reference](./README.md)

## ErrorDefinitionDiscovery

```php
public function discover(): DiscoveryResult
```

Method membaca root Composer autoload pada pemanggilan pertama dan menggunakan in-memory snapshot untuk pemanggilan berikutnya dalam process yang sama.

## DiscoveryResult

```php
final readonly class DiscoveryResult
{
    /**
     * @param list<class-string<ErrorCode>> $errorEnums
     * @param list<class-string<FormRequest>> $formRequests
     */
    public function __construct(
        public array $errorEnums,
        public array $formRequests,
    ) {}
}
```

Kedua daftar:

- berisi FQCN, bukan object atau enum case;
- tidak memiliki duplicate FQCN;
- diurutkan ascending;
- hanya berasal dari root application autoload;
- tidak menyertakan `autoload-dev` atau `vendor/`.

## Candidate Sources

| Composer Mapping | Cara Diproses |
|------------------|---------------|
| `autoload.psr-4` | FQCN dibentuk dari namespace prefix dan relative file path. |
| `autoload.classmap` | Menggunakan class-to-file mapping Composer pada path terkait. |
| `autoload-dev` | Tidak diproses. |
| dependency `vendor/` | Tidak diproses. |

PSR-4 file dimuat melalui Composer autoloader. Discovery tidak menjalankan `require_once` terhadap seluruh file.

## Target Filters

### Error Enum

- merupakan enum;
- mengimplementasikan `ErrorCode`;
- backing type dan attribute belum divalidasi.

### FormRequest

- merupakan concrete subclass `FormRequest`;
- menggunakan `HasErrorDefinitions` secara langsung atau melalui inheritance.

## DiscoveryException

`DiscoveryException` dilempar ketika:

- root `composer.json` tidak ditemukan atau tidak valid;
- autoload path tidak dapat dibaca;
- Composer autoloader gagal memuat candidate.

Exception mempertahankan previous `Throwable` ketika kegagalan berasal dari proses autoload.

## Consumers

```php
$targets = $discovery->discover();

$report = $linter->lint(
    errorEnums: $targets->errorEnums,
    formRequests: $targets->formRequests,
);
```

Generator menggunakan `errorEnums` dari result yang sama. Discovery tidak membaca `ErrorDefinition` dan tidak menghasilkan `ResolvedErrorDefinition`.
