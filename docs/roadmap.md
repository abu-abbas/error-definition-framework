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
- [ ] 1.10 Generator `error-codes.ts`
- [ ] 1.11 Generator `error-catalog.json`

## Stage 2 — Central Error Registry

- [ ] Publish catalog dari aplikasi
- [ ] Validasi schema
- [ ] Versioning
- [ ] Duplicate detection
- [ ] Ownership
- [ ] Pencarian
- [ ] Sinkronisasi lintas aplikasi
- [ ] Dashboard registry
- [ ] Integrasi dengan sistem lain
