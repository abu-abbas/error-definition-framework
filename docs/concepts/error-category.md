# Error Category

[← Kembali ke Concepts](./README.md)

> **TL;DR:** category menjawab "error ini terjadi karena jenis masalah apa?", bukan "seberapa parah dampaknya?".

Ketika nomor surat belum diisi, penyebabnya adalah validation. Ketika surat tidak dapat diubah karena sudah masuk tahap persetujuan, penyebabnya adalah workflow. Keduanya dapat sama-sama menghentikan satu tindakan, tetapi berasal dari jenis masalah yang berbeda.

## Baseline Category

| Category | Makna bisnis |
|----------|--------------|
| Validation | Data yang diberikan belum memenuhi aturan input. |
| Authentication | Identitas pengguna belum atau tidak dapat diverifikasi. |
| Authorization | Pengguna tidak memiliki hak untuk melakukan tindakan. |
| Not Found | Data atau resource yang diminta tidak ditemukan. |
| Business Rule | Tindakan bertentangan dengan aturan bisnis. |
| Workflow | Tindakan tidak sesuai dengan status atau tahapan proses. |
| Integration | Pertukaran data dengan sistem lain mengalami masalah. |
| System | Aplikasi atau infrastrukturnya mengalami kegagalan internal. |

Category dipilih berdasarkan penyebab error, bukan berdasarkan halaman tempat error muncul. Daftar ini dapat diperluas ketika terdapat jenis penyebab baru yang benar-benar berbeda.
