# Error Code

[← Kembali ke Concepts](./README.md)

> **TL;DR:** error code adalah identitas tetap untuk sebuah kondisi gagal, seperti nomor perkara yang tidak berubah meskipun penjelasannya diperbarui.

Contoh `SM-WF-001` dapat digunakan untuk menandai bahwa draft Surat Masuk tidak dapat diubah karena status workflow tertentu. Pengguna melihat pesan yang mudah dipahami, sedangkan developer, QA, log, dan helpdesk menggunakan kode yang sama untuk mengenali kejadian tersebut.

## Bagian Error Code

Sebuah error code terdiri dari:

- prefix modul atau fitur, misalnya `SM` untuk Surat Masuk;
- singkatan category atau konteks, misalnya `WF` untuk workflow;
- nomor urut, misalnya `001`.

Error code yang telah digunakan tidak boleh diberikan kepada kondisi lain. Pesan dapat diperbaiki, tetapi identitas error harus tetap stabil agar log, integrasi, dan dokumentasi tidak salah menghubungkan kejadian.

Cara menyusun enum dan prefix dijelaskan pada [panduan pengorganisasian error](../guides/organizing-error-enums.md). Format kode diperiksa oleh [Error Definition Linter](../decisions/1-error-definition-framework/1.8-validator-dan-linter.md).
