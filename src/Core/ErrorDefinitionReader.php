<?php

declare(strict_types=1);

namespace Edf\Core;

use BackedEnum;
use Edf\Core\Attributes\ErrorDefinition;
use Edf\Core\Exceptions\MissingErrorDefinitionException;
use ReflectionEnumBackedCase;

/**
 * Membaca satu enum case error dan mengembalikan definisi yang telah di-resolve.
 *
 * Hasil resolve disimpan pada in-memory cache selama lifecycle Reader. Cache
 * hanya berisi metadata immutable sehingga aman untuk Octane dan Queue Worker.
 *
 * @see docs/decisions/1-error-definition-framework/1.3-error-definition-reader.md
 * @see docs/decisions/1-error-definition-framework/1.3.1-caching-dan-siklus-proses.md
 */
final class ErrorDefinitionReader
{
    /** @var array<class-string, array<string, ResolvedErrorDefinition>> */
    private array $cache = [];

    public function read(ErrorCode&BackedEnum $error): ResolvedErrorDefinition
    {
        return $this->cache[$error::class][$error->name] ??= $this->resolve($error);
    }

    private function resolve(ErrorCode&BackedEnum $error): ResolvedErrorDefinition
    {
        $case = new ReflectionEnumBackedCase($error, $error->name);
        $attributes = $case->getAttributes(ErrorDefinition::class);

        if ($attributes === []) {
            throw MissingErrorDefinitionException::for($error);
        }

        $definition = $attributes[0]->newInstance();

        return new ResolvedErrorDefinition(
            code: (string) $error->value,
            message: $definition->message,
            category: $definition->category,
            httpStatus: $definition->httpStatus,
            severity: $definition->severity,
            retryable: $definition->retryable,
        );
    }
}
