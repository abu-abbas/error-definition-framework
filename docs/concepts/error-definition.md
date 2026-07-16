# Error Definition

`ErrorDefinition` adalah metadata standar yang melekat pada setiap enum case error.

Metadata ini menjadi source of truth untuk menentukan bagaimana error direpresentasikan oleh exception, validation, response, logging, dan generator.

## Metadata

| Field | Keterangan |
|-------|------------|
| `message` | Pesan default yang aman ditampilkan kepada pengguna. |
| `category` | Jenis atau konteks penyebab error. |
| `httpStatus` | HTTP status yang digunakan pada response API. |
| `severity` | Tingkat dampak error terhadap pengguna, proses, atau layanan. |
| `retryable` | Menentukan apakah operasi layak dicoba kembali. |

## Metadata di Luar Runtime

Knowledge Base, owner, SLA, escalation, registry metadata, dan module tidak menjadi bagian dari `ErrorDefinition` runtime. Metadata tersebut dapat dikelola oleh komponen operasional atau Central Error Registry.

Error code juga tidak disimpan pada attribute karena berasal dari value PHP Backed Enum.
