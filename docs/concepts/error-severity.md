# Error Severity

[← Kembali ke Concepts](./README.md)

> **TL;DR:** severity menjawab "seberapa besar dampak error ini terhadap pekerjaan pengguna atau layanan?".

Form yang belum lengkap biasanya berdampak rendah karena pengguna dapat memperbaikinya sendiri. Sebaliknya, layanan tanda tangan elektronik yang tidak tersedia dapat berdampak tinggi karena menghentikan proses penting bagi banyak pengguna.

## Tingkat Dampak

| Severity | Makna bisnis |
|----------|--------------|
| Low | Dampak terbatas; pengguna masih dapat melanjutkan pekerjaan setelah koreksi sederhana. |
| Medium | Satu operasi gagal atau memerlukan perhatian, tetapi layanan utama masih tersedia. |
| High | Fitur atau proses penting tidak dapat digunakan dan memerlukan penanganan. |
| Critical | Layanan utama terganggu, berdampak luas, atau berisiko menyebabkan kerusakan serius. |

Severity digunakan untuk membantu logging, monitoring, prioritas penanganan, dan analisis operasional. Severity tidak menjelaskan penyebab error; informasi tersebut merupakan tugas [Error Category](./error-category.md).
