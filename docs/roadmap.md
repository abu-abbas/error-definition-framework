# Roadmap

[← Kembali ke documentation index](./README.md)

Roadmap mencatat status keputusan desain. Checklist `[x]` berarti kontrak dan decision doc telah disepakati, bukan implementasi kode telah selesai. Implementasi akan dilacak secara terpisah setelah tahap desain selesai.

Alasan dan keputusan desain didokumentasikan secara terpisah pada [Decisions](./decisions/).

## Stage 1 — Error Definition Framework

- [x] [1.1 Error Definition](./decisions/1-error-definition-framework/1.1-error-definition.md)
- [x] [1.2 Struktur Enum Error per Modul](./decisions/1-error-definition-framework/1.2-struktur-enum-error-per-modul.md)
- [x] [1.3 ErrorDefinitionReader](./decisions/1-error-definition-framework/1.3-error-definition-reader.md)
- [x] [1.4 Application Exception](./decisions/1-error-definition-framework/1.4-application-exception.md)
- [x] [1.5 Validation Error Mapping](./decisions/1-error-definition-framework/1.5-validation-error-mapping.md)
- [x] [1.6 Format Response JSON Standar](./decisions/1-error-definition-framework/1.6-format-response-json-standar.md)
- [x] [1.7 Logging & Context](./decisions/1-error-definition-framework/1.7-logging-dan-context.md)
- [x] [1.8 Validator / Linter](./decisions/1-error-definition-framework/1.8-validator-dan-linter.md)
- [x] [1.9 Error Definition Discovery Mechanism](./decisions/1-error-definition-framework/1.9-error-definition-discovery-mechanism.md)
- [x] [1.10 Generator `error-codes.ts`](./decisions/1-error-definition-framework/1.10-generator-error-codes-typescript.md)
- [x] [1.11 Generator `error-catalog.json`](./decisions/1-error-definition-framework/1.11-generator-error-catalog-json.md)

## Stage 2 — Central Error Registry

- [x] [2.1 Publish Catalog dari Aplikasi](./decisions/2-central-error-registry/2.1-publish-catalog-dari-aplikasi.md)
- [x] [2.2 Validasi Schema](./decisions/2-central-error-registry/2.2-validasi-schema.md)
- [x] [2.3 Deteksi Error Code Duplikat](./decisions/2-central-error-registry/2.3-deteksi-error-code-duplikat.md)
- [x] [2.4 Ownership](./decisions/2-central-error-registry/2.4-ownership.md)
- [x] [2.5 Versioning](./decisions/2-central-error-registry/2.5-versioning.md)
- [x] [2.6 Pencarian](./decisions/2-central-error-registry/2.6-pencarian.md)
- [x] [2.7 Sinkronisasi Lintas Aplikasi](./decisions/2-central-error-registry/2.7-sinkronisasi-lintas-aplikasi.md)
- [ ] [2.8 Dashboard Registry](./decisions/2-central-error-registry/2.8-dashboard-registry.md)
- [ ] [2.9 Integrasi dengan Sistem Lain](./decisions/2-central-error-registry/2.9-integrasi-dengan-sistem-lain.md)
