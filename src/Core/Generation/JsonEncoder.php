<?php

declare(strict_types=1);

namespace Edf\Core\Generation;

use JsonException;

/**
 * Serialization JSON bersama untuk catalog dan publish payload: pretty print
 * dua spasi, tanpa escape yang tidak diperlukan, dan satu newline di akhir.
 *
 * @see docs/decisions/1-error-definition-framework/1.11-generator-error-catalog-json.md#serialization
 */
final class JsonEncoder
{
    /**
     * @param array<string, mixed> $data
     */
    public static function encode(array $data, string $artifact): string
    {
        try {
            $json = json_encode(
                $data,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
            );
        } catch (JsonException $e) {
            throw GenerationException::encodingFailed($artifact, $e);
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
