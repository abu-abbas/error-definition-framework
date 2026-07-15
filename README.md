# Error Definition Framework

> Sebuah framework untuk mendefinisikan, mengelola, dan membagikan error secara terstruktur, konsisten, dan mudah diintegrasikan ke berbagai kebutuhan operasional.

## Latar Belakang

Pada umumnya, error di dalam aplikasi tersebar di berbagai tempat, seperti validation, exception, service, controller, hingga frontend. Akibatnya:

* Error code tidak memiliki standar.
* Pesan error mudah terduplikasi.
* Sulit didokumentasikan.
* Sulit diintegrasikan dengan sistem lain seperti monitoring, knowledge base, maupun helpdesk.
* Setiap aplikasi memiliki cara penanganan error yang berbeda.

**Error Definition Framework** hadir untuk menjadikan error sebagai **aset aplikasi** yang memiliki definisi baku dan dapat dimanfaatkan oleh berbagai kebutuhan, tanpa mengubah cara developer membangun aplikasi sehari-hari.

Framework ini menjadikan **Laravel sebagai source of truth**, sedangkan aplikasi lain (Vue, Mobile, dan lain-lain) cukup menggunakan hasil generate yang disediakan.

---

# Roadmap

Pengembangan dibagi menjadi dua tahap besar.

## Tahap 1 — Error Definition Framework

Tahap ini berfokus pada bagaimana **setiap aplikasi** mendefinisikan dan mengelola error secara konsisten.

Cakupan pembahasan:

* Struktur error
* Throw / Catch / Render
* Penamaan error code
* Kategori
* Severity
* Metadata
* Integrasi Validation
* Logging
* Generate manifest

### Roadmap Implementasi

| Status | Topik |
|--------|-------|
| ✅ | [1.1 ErrorDefinition](./roadmaps/1-error-definition-framework/1.1-error-definition.md) |
| ✅ | [1.2 Struktur Enum Error per Modul](./roadmaps/1-error-definition-framework/1.2-struktur-enum-error-per-modul.md) |
| 🚧 | [1.3 ErrorDefinitionReader](./roadmaps/1-error-definition-framework/1.3-error-definition-reader.md) |
| ⏳ | 1.4 ApplicationException |
| ⏳ | 1.5 Integrasi FormRequest |
| ⏳ | 1.6 Format Response JSON Standar |
| ⏳ | 1.7 Logging & Context |
| ⏳ | 1.8 Validator / Linter |
| ⏳ | 1.9 Generator `error-codes.ts` |
| ⏳ | 1.10 Generator `error-catalog.json` |

---

Tahap ini menjadi pondasi utama. Selama sepuluh poin tersebut belum stabil, pembahasan tidak akan berlanjut ke registry terpusat.

---

## Tahap 2 — Central Error Registry

Setelah setiap aplikasi memiliki Error Definition Framework yang matang, tahap berikutnya adalah membangun **Central Error Registry**.

Fokus pembahasan:

* Publish catalog dari aplikasi
* Validasi schema
* Versioning
* Duplicate detection
* Ownership
* Pencarian
* Sinkronisasi lintas aplikasi
* Dashboard registry
* Integrasi dengan sistem lain

Central Error Registry **bukan source of truth**, melainkan tempat mengumpulkan error definition dari berbagai aplikasi.

---

# Prinsip Desain

Framework ini dibangun dengan beberapa prinsip berikut:

* Laravel merupakan **source of truth**.
* Error hanya didefinisikan satu kali.
* Metadata error ditempel menggunakan **PHP Attribute**.
* Error code direpresentasikan menggunakan **PHP Backed Enum**.
* Frontend tidak mendefinisikan ulang error, tetapi menggunakan hasil generate.
* Framework tidak bergantung pada struktur folder tertentu.
* Runtime aplikasi tidak bergantung pada Central Error Registry.
* Central Error Registry hanya menjadi konsumen dari Error Definition Framework.

---

# Resource yang Akan Dihasilkan

Framework akan menghasilkan beberapa resource yang dapat digunakan oleh aplikasi lain.

```text
Laravel
│
├── error-codes.ts
└── error-catalog.json
```

Dengan pendekatan ini, frontend dapat menggunakan error code dan metadata yang sama tanpa melakukan duplikasi.

---

# Gambaran Arsitektur

```text
Aplikasi Laravel
        │
        ▼
Error Definition Framework
        │
        ├── Exception
        ├── Validation
        ├── Logging
        ├── Generator TypeScript
        └── Generator Error Catalog
                │
                ▼
Central Error Registry
        │
        ├── Knowledge Base
        ├── Search
        ├── Ownership
        ├── Analytics
        ├── Monitoring
        └── Integrasi ITSM
```

---

# Status Saat Ini

Repository ini saat ini masih berfokus pada **Tahap 1**, yaitu membangun pondasi **Error Definition Framework**.

Target utamanya adalah menghasilkan mekanisme pendefinisian error yang konsisten, mudah digunakan oleh developer, serta dapat menghasilkan berbagai resource secara otomatis.

Setelah pondasi tersebut matang dan stabil, barulah pengembangan dilanjutkan ke **Central Error Registry** sebagai layanan terpisah.
