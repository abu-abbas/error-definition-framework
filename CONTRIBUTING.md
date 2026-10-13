# Contributing

Terima kasih telah berkontribusi pada Error Definition Framework.

## Dokumentasi

Dokumentasi dipisahkan berdasarkan tujuan:

- `concepts/` menjelaskan apa konsepnya dengan bahasa non-teknis;
- `decisions/` menjelaskan keputusan dan alasannya;
- `reference/` menjelaskan API dan kontrak;
- `guides/` menjelaskan cara penggunaan;
- `roadmap.md` mencatat status pekerjaan.

### Decision Doc

Semua decision doc baru wajib dimulai dari [template ADR](./docs/decisions/_template.md) dan mengikuti urutan:

```text
Tujuan → Keputusan → Alasan → Keputusan Terkait → Konsekuensi
```

Decision doc harus memiliki TL;DR non-teknis. Aturan yang membutuhkan judgment wajib menyertakan contoh dan counter-example.

Detail implementasi, contoh cara pakai, dan struktur folder ditempatkan pada `guides/`, bukan `decisions/`.

Istilah teknis baru yang digunakan lintas dokumen harus ditambahkan ke [glossary](./docs/concepts/istilah.md).

### Configuration

Config key baru wajib:

- dikelompokkan berdasarkan feature area, misalnya `logging.additional_sensitive_keys`, bukan `additional_sensitive_keys` pada root config;
- menggunakan `snake_case`;
- didaftarkan pada [Configuration Reference](./docs/reference/configuration.md) dalam pull request yang sama dengan decision doc yang menetapkannya.

## Kode

Package terdiri dari dua namespace:

- `Edf\Core\` berisi kontrak dan logic PHP murni. Core tidak boleh meng-import `Illuminate\` maupun `Edf\Laravel\` agar update Laravel hanya menyentuh adapter.
- `Edf\Laravel\` berisi adapter tipis untuk Laravel, seperti service provider dan integrasi framework.

Jalankan test dengan:

```bash
composer install
composer test
```

`composer install` juga memasang pre-push hook dari `.githooks/` melalui `git config core.hooksPath`. Hook menjalankan PHPUnit sebelum `git push` dan membatalkan push jika test gagal. GitHub Actions menjalankan test yang sama pada setiap pull request yang mengubah kode, dengan matrix PHP 8.2 + Laravel 11 dan PHP 8.4 + Laravel 12. Perubahan yang hanya menyentuh dokumentasi tidak menjalankan CI.

## Sebelum Mengirim Perubahan

- Pastikan seluruh link Markdown lokal valid.
- Pastikan tidak ada whitespace error dengan `git diff --check`.
- Pastikan `composer test` lulus jika mengubah kode.
- Perbarui index dokumentasi jika menambah atau memindahkan file.
- Jangan mengubah keputusan teknis yang sudah disepakati tanpa decision doc baru atau pembaruan yang menjelaskan alasannya.
