# Documentation

[← Kembali ke repository](../README.md)

Dokumentasi Error Definition Framework dibagi berdasarkan tujuan informasi.

| Bagian | Pertanyaan yang dijawab |
|--------|-------------------------|
| [Concepts](./concepts/) | Apa konsepnya? |
| [Decisions](./decisions/) | Mengapa desainnya seperti ini? |
| [Reference](./reference/) | Apa API dan kontraknya? |
| [Guides](./guides/) | Bagaimana cara menggunakannya? |
| [Roadmap](./roadmap.md) | Apa yang sudah dan akan dikerjakan? |

## Design Decisions

### 1. Error Definition Framework

- [1.1 Error Definition](./decisions/1-error-definition-framework/1.1-error-definition.md)
  - [1.1.1 Batas Metadata Runtime dan Registry](./decisions/1-error-definition-framework/1.1.1-batas-metadata-runtime-dan-registry.md)
- [1.2 Struktur Enum Error per Modul](./decisions/1-error-definition-framework/1.2-struktur-enum-error-per-modul.md)
- [1.3 ErrorDefinitionReader](./decisions/1-error-definition-framework/1.3-error-definition-reader.md)
  - [1.3.1 Caching dan Siklus Proses](./decisions/1-error-definition-framework/1.3.1-caching-dan-siklus-proses.md)
- [1.4 ApplicationException](./decisions/1-error-definition-framework/1.4-application-exception.md)
- [1.5 Validation Error Mapping](./decisions/1-error-definition-framework/1.5-validation-error-mapping.md)
- [1.6 Format Response JSON Standar](./decisions/1-error-definition-framework/1.6-format-response-json-standar.md)
- [1.7 Logging dan Context](./decisions/1-error-definition-framework/1.7-logging-dan-context.md)
- [1.8 Validator dan Linter](./decisions/1-error-definition-framework/1.8-validator-dan-linter.md)
- [1.9 Error Definition Discovery Mechanism](./decisions/1-error-definition-framework/1.9-error-definition-discovery-mechanism.md)
- [1.10 Generator `error-codes.ts`](./decisions/1-error-definition-framework/1.10-generator-error-codes-typescript.md)
- [1.11 Generator `error-catalog.json`](./decisions/1-error-definition-framework/1.11-generator-error-catalog-json.md)
