<?php

declare(strict_types=1);

namespace Edf\Laravel\Generation;

use BackedEnum;
use Edf\Core\ErrorDefinitionReader;
use Edf\Core\Generation\ErrorCatalogJsonGenerator;
use Edf\Core\Generation\ErrorCodesTypeScriptGenerator;
use Edf\Core\Generation\GenerationException;
use Edf\Core\Lint\ErrorDefinitionRules;
use Edf\Core\ResolvedErrorDefinition;
use Edf\Laravel\Discovery\ErrorDefinitionDiscovery;
use Illuminate\Contracts\Config\Repository;
use Throwable;

/**
 * Shared generation pipeline: discovery, gate EDF001–EDF008, resolve melalui
 * Reader, lalu membentuk seluruh content artifact di memory sebelum ada write.
 *
 * @see docs/decisions/1-error-definition-framework/1.10-generator-error-codes-typescript.md
 * @see docs/decisions/1-error-definition-framework/1.11-generator-error-catalog-json.md
 * @see docs/decisions/2-publish-catalog/2.1-publish-catalog-dari-aplikasi.md
 */
final class ArtifactGenerator
{
    public function __construct(
        private readonly ErrorDefinitionDiscovery $discovery,
        private readonly ErrorDefinitionRules $rules,
        private readonly ErrorDefinitionReader $reader,
        private readonly ErrorCodesTypeScriptGenerator $typeScript,
        private readonly ErrorCatalogJsonGenerator $catalog,
        private readonly Repository $config,
    ) {}

    /**
     * @return array<string, string> output path => complete file content
     */
    public function artifacts(): array
    {
        $typeScriptPath = $this->path('typescript_path');
        $catalogPath = $this->path('catalog_path');

        if ($typeScriptPath === $catalogPath) {
            throw GenerationException::invalidPath('error-definition.generation.catalog_path');
        }

        $definitions = $this->definitions();

        return [
            $typeScriptPath => $this->typeScript->generate($definitions),
            $catalogPath => $this->catalog->generate($definitions),
        ];
    }

    /**
     * Catalog di memory untuk publish, tanpa membaca atau menulis catalog_path.
     *
     * @return array{schemaVersion: int, errors: list<array<string, mixed>>}
     */
    public function catalog(): array
    {
        return $this->catalog->catalog($this->definitions());
    }

    private function path(string $key): string
    {
        $path = $this->config->get('error-definition.generation.'.$key);

        if (! is_string($path) || $path === '') {
            throw GenerationException::invalidPath('error-definition.generation.'.$key);
        }

        return $path;
    }

    /**
     * @return list<ResolvedErrorDefinition>
     */
    private function definitions(): array
    {
        $errorEnums = $this->discovery->discover()->errorEnums;
        $findings = $this->rules->check($errorEnums);

        if ($findings !== []) {
            throw GenerationException::lintFailed($findings);
        }

        $definitions = [];

        foreach ($errorEnums as $enum) {
            /** @var class-string<BackedEnum> $enum */
            foreach ($enum::cases() as $case) {
                try {
                    $definitions[] = $this->reader->read($case);
                } catch (Throwable $e) {
                    throw GenerationException::resolveFailed($enum.'::'.$case->name, $e);
                }
            }
        }

        usort($definitions, fn (ResolvedErrorDefinition $a, ResolvedErrorDefinition $b): int => strcmp($a->code, $b->code));

        return $definitions;
    }
}
