<?php

declare(strict_types=1);

namespace Edf\Laravel\Discovery;

use Edf\Core\Discovery\ComposerAutoloadCandidates;
use Edf\Core\Discovery\DiscoveryException;
use Edf\Core\ErrorCode;
use Edf\Laravel\Validation\HasErrorDefinitions;
use Illuminate\Foundation\Http\FormRequest;
use ReflectionClass;
use Throwable;

/**
 * Menemukan enum error dan FormRequest dari root Composer autoload aplikasi.
 *
 * Hasil disimpan sebagai in-memory snapshot pada pemanggilan pertama; discovery
 * tidak berjalan ketika aplikasi boot.
 *
 * @see docs/decisions/1-error-definition-framework/1.9-error-definition-discovery-mechanism.md
 */
final class ErrorDefinitionDiscovery
{
    private ?DiscoveryResult $snapshot = null;

    public function __construct(
        private readonly string $basePath,
    ) {}

    public function discover(): DiscoveryResult
    {
        return $this->snapshot ??= $this->scan();
    }

    private function scan(): DiscoveryResult
    {
        $errorEnums = [];
        $formRequests = [];

        foreach ((new ComposerAutoloadCandidates($this->basePath))->find() as $class => $path) {
            if (! $this->load($class, $path)) {
                continue;
            }

            if (enum_exists($class, false)) {
                if (is_subclass_of($class, ErrorCode::class)) {
                    $errorEnums[] = $class;
                }

                continue;
            }

            if (class_exists($class, false)
                && is_subclass_of($class, FormRequest::class)
                && ! (new ReflectionClass($class))->isAbstract()
                && in_array(HasErrorDefinitions::class, class_uses_recursive($class), true)) {
                $formRequests[] = $class;
            }
        }

        return new DiscoveryResult($this->normalize($errorEnums), $this->normalize($formRequests));
    }

    private function load(string $class, string $path): bool
    {
        try {
            return class_exists($class) || interface_exists($class) || trait_exists($class);
        } catch (Throwable $e) {
            throw DiscoveryException::candidateFailed($class, $path, $e);
        }
    }

    /**
     * @template T of string
     * @param list<T> $classes
     * @return list<T>
     */
    private function normalize(array $classes): array
    {
        $classes = array_values(array_unique($classes));
        sort($classes, SORT_STRING);

        return $classes;
    }
}
