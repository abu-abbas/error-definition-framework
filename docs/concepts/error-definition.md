# Error Definition

[← Kembali ke Concepts](./README.md)

> **TL;DR:** setiap error dicatat satu kali bersama pesan dan karakteristiknya, sehingga backend, frontend, QA, dan dokumentasi membicarakan error yang sama.

Bayangkan sebuah surat tidak dapat diubah karena sudah dikunci oleh approver. Tanpa definisi bersama, backend dapat menyebutnya "draft locked", frontend menampilkan pesan berbeda, dan QA mencatatnya sebagai error lain. Error Definition menyatukan semua informasi dasar tersebut.

## Informasi yang Dimiliki Error

| Informasi | Makna |
|-----------|-------|
| Pesan | Penjelasan default yang aman ditampilkan kepada pengguna. |
| Category | Kelompok penyebab error, misalnya validation atau workflow. |
| HTTP status | Status standar yang dikirim melalui API. |
| Severity | Tingkat dampak error terhadap pekerjaan pengguna atau layanan. |
| Retryable | Penanda apakah operasi layak dicoba kembali. |

## Batas Informasi Runtime

Informasi yang dibutuhkan aplikasi ketika berjalan disimpan bersama Error Definition. Informasi organisasional seperti pemilik layanan, SLA, jalur eskalasi, dan artikel Knowledge Base dikelola terpisah karena dapat berubah tanpa perubahan aplikasi.

Penjelasan istilah teknis tersedia pada [glossary](./istilah.md). Alasan pemisahan metadata dijelaskan pada [Decision 1.1.1](../decisions/1-error-definition-framework/1.1.1-batas-metadata-runtime-dan-registry.md).
