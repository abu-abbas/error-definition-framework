# Error Severity

`ErrorSeverity` menunjukkan tingkat dampak error terhadap pengguna, proses bisnis, atau layanan.

Baseline severity yang umum digunakan:

```php
enum ErrorSeverity: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case CRITICAL = 'critical';
}
```

| Severity | Dampak |
|----------|--------|
| `LOW` | Dampak terbatas dan proses utama masih dapat dilanjutkan. |
| `MEDIUM` | Operasi tertentu gagal, tetapi layanan masih dapat digunakan. |
| `HIGH` | Fitur atau proses penting tidak dapat digunakan dan memerlukan perhatian. |
| `CRITICAL` | Layanan utama terganggu atau berpotensi menyebabkan dampak luas. |

Severity digunakan oleh logging, monitoring, dan analisis operasional. Severity tidak menentukan penyebab error.
