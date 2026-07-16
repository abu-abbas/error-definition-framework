# Error Category

`ErrorCategory` mengelompokkan error berdasarkan jenis atau konteks penyebabnya. Category tidak menunjukkan tingkat dampak error.

Baseline category yang umum digunakan:

```php
enum ErrorCategory: string
{
    case VALIDATION = 'validation';
    case AUTHENTICATION = 'authentication';
    case AUTHORIZATION = 'authorization';
    case NOT_FOUND = 'not_found';
    case BUSINESS_RULE = 'business_rule';
    case WORKFLOW = 'workflow';
    case INTEGRATION = 'integration';
    case SYSTEM = 'system';
}
```

Category dipilih berdasarkan penyebab error, bukan berdasarkan halaman tempat error ditampilkan. Nilai dapat diperluas sesuai kebutuhan aplikasi tanpa mengubah makna nilai yang sudah digunakan.
