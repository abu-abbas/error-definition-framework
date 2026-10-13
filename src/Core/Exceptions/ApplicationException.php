<?php

declare(strict_types=1);

namespace Edf\Core\Exceptions;

use BackedEnum;
use Edf\Core\ErrorCode;
use Edf\Core\ErrorDefinitionReader;
use Edf\Core\ResolvedErrorDefinition;
use RuntimeException;
use Throwable;

/**
 * Exception standar aplikasi yang hanya berfungsi sebagai data carrier.
 *
 * Message selalu berasal dari definition dan tidak dapat di-override.
 * Exception::$code tetap 0; string error code tersedia melalui definition.
 * Context tidak otomatis dikirim ke client dan tidak disanitasi di sini;
 * sanitasi dilakukan pada logging boundary.
 *
 * Shortcut of() hanya mendelegasikan resolve ke ErrorDefinitionReader;
 * constructor tetap menerima ResolvedErrorDefinition.
 *
 * @see docs/decisions/1-error-definition-framework/1.4-application-exception.md
 */
final class ApplicationException extends RuntimeException
{
    private static ?ErrorDefinitionReader $reader = null;

    /**
     * @param array<string, mixed> $context
     */
    public function __construct(
        public readonly ResolvedErrorDefinition $definition,
        public readonly array $context = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            message: $definition->message,
            code: 0,
            previous: $previous,
        );
    }

    /**
     * Membuat exception langsung dari enum case, misalnya
     * `throw ApplicationException::of(SuratMasukError::DRAFT_LOCKED, ['surat_id' => $id]);`
     *
     * @param array<string, mixed> $context
     */
    public static function of(ErrorCode&BackedEnum $error, array $context = [], ?Throwable $previous = null): self
    {
        return new self((self::$reader ??= new ErrorDefinitionReader())->read($error), $context, $previous);
    }

    /**
     * Dipanggil adapter framework agar of() memakai Reader yang sama dengan
     * aplikasi, sehingga cache definition dipakai bersama.
     */
    public static function useReader(ErrorDefinitionReader $reader): void
    {
        self::$reader = $reader;
    }
}
