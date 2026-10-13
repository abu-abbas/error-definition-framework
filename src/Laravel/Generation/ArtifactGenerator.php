<?php

declare(strict_types=1);

namespace Edf\Laravel\Generation;

use BackedEnum;
use Edf\Core\ErrorDefinitionReader;
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
 */
final class ArtifactGenerator
{
    public function __construct(
        private readonly ErrorDefinitionDiscovery $discovery,
        private readonly ErrorDefinitionRules $rules,
        private readonly ErrorDefinitionReader $reader,
        private readonly ErrorCodesTypeScriptGenerator $typeScript,
        private readonly Repository $config,
    ) {}

    /**
     * @return array<string, string> output path => complete file content
     */
    public function artifacts(): array
    {
        $definitions = $this->definitions();

        return [
            $this->path('typescript_path') => $this->typeScript->generate($definitions),
        ];
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
