<?php

declare(strict_types=1);

namespace Edf\Core\Generation;

use Edf\Core\ResolvedErrorDefinition;

/**
 * Membentuk isi `error-catalog.json`: seluruh metadata runtime per error code,
 * tanpa source identity, timestamp, atau enrichment dari luar aplikasi.
 *
 * @see docs/decisions/1-error-definition-framework/1.11-generator-error-catalog-json.md
 */
final class ErrorCatalogJsonGenerator
{
    public const int SCHEMA_VERSION = 1;

    /**
     * @param iterable<ResolvedErrorDefinition> $definitions
     */
    public function generate(iterable $definitions): string
    {
        return JsonEncoder::encode($this->catalog($definitions), 'error-catalog.json');
    }

    /**
     * Catalog dalam bentuk array, dipakai juga sebagai isi publish payload.
     *
     * @param iterable<ResolvedErrorDefinition> $definitions
     * @return array{schemaVersion: int, errors: list<array<string, mixed>>}
     */
    public function catalog(iterable $definitions): array
    {
        $errors = [];

        foreach ($definitions as $definition) {
            $errors[$definition->code] = [
                'code' => $definition->code,
                'message' => $definition->message,
                'category' => $definition->category->value,
                'httpStatus' => $definition->httpStatus,
                'severity' => $definition->severity->value,
                'retryable' => $definition->retryable,
            ];
        }

        ksort($errors, SORT_STRING);

        return ['schemaVersion' => self::SCHEMA_VERSION, 'errors' => array_values($errors)];
    }
}
