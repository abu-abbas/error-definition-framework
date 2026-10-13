<?php

declare(strict_types=1);

namespace Edf\Core\Generation;

use Edf\Core\ResolvedErrorDefinition;
use JsonException;

/**
 * Membentuk isi `error-catalog.json`: seluruh metadata runtime per error code,
 * tanpa source identity, timestamp, atau enrichment registry.
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

        try {
            $json = json_encode(
                ['schemaVersion' => self::SCHEMA_VERSION, 'errors' => array_values($errors)],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
            );
        } catch (JsonException $e) {
            throw GenerationException::encodingFailed('error-catalog.json', $e);
        }

        // PHP memakai indentasi empat spasi; kontrak catalog memakai dua spasi.
        $json = preg_replace_callback(
            '/^(?: {4})+/m',
            fn (array $match): string => str_repeat('  ', intdiv(strlen($match[0]), 4)),
            $json,
        );

        return $json."\n";
    }
}
