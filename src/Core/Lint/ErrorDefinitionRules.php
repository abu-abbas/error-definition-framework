<?php

declare(strict_types=1);

namespace Edf\Core\Lint;

use BackedEnum;
use Edf\Core\Attributes\ErrorDefinition;
use Edf\Core\ErrorCode;
use ReflectionEnum;
use ReflectionEnumBackedCase;
use ReflectionNamedType;
use Throwable;

/**
 * Rule EDF001–EDF008 untuk enum error. Seluruh temuan dikumpulkan; pemeriksaan
 * tidak berhenti pada pelanggaran pertama.
 *
 * @see docs/decisions/1-error-definition-framework/1.8-validator-dan-linter.md
 */
final class ErrorDefinitionRules
{
    public const string CODE_FORMAT = '/^[A-Z0-9]+(?:-[A-Z0-9]+)+-\d{3,}$/';

    /**
     * @param iterable<mixed> $errorEnums
     * @return list<LintFinding>
     */
    public function check(iterable $errorEnums): array
    {
        $findings = [];
        /** @var array<string, string> $codes error code => source pertama */
        $codes = [];
        $checked = [];

        foreach ($errorEnums as $target) {
            if (is_string($target) && isset($checked[$target])) {
                continue;
            }

            if (! is_string($target) || ! enum_exists($target)
                || ! is_subclass_of($target, ErrorCode::class)
                || ! is_subclass_of($target, BackedEnum::class)) {
                $findings[] = $this->error('EDF001', is_string($target) ? $target : get_debug_type($target),
                    'Target bukan enum yang mengimplementasikan ErrorCode dan BackedEnum.');

                continue;
            }

            $checked[$target] = true;
            $enum = new ReflectionEnum($target);
            $type = $enum->getBackingType();

            if (! $type instanceof ReflectionNamedType || $type->getName() !== 'string') {
                $findings[] = $this->error('EDF002', $target, 'Enum error wajib menggunakan string backing.');

                continue;
            }

            foreach ($enum->getCases() as $case) {
                /** @var ReflectionEnumBackedCase $case */
                $this->checkCase($case, $target.'::'.$case->getName(), $findings, $codes);
            }
        }

        return $findings;
    }

    /**
     * @param list<LintFinding> $findings
     * @param array<string, string> $codes
     */
    private function checkCase(ReflectionEnumBackedCase $case, string $source, array &$findings, array &$codes): void
    {
        $code = (string) $case->getBackingValue();
        $attributes = $case->getAttributes(ErrorDefinition::class);
        $definition = null;

        if ($attributes === []) {
            $findings[] = $this->error('EDF003', $source, 'ErrorDefinition attribute tidak ditemukan.');
        } elseif (count($attributes) > 1) {
            $findings[] = $this->error('EDF004', $source, 'Enum case memiliki lebih dari satu ErrorDefinition attribute.');
        } else {
            try {
                $definition = $attributes[0]->newInstance();
            } catch (Throwable $e) {
                $findings[] = $this->error('EDF003', $source, 'ErrorDefinition attribute tidak dapat dibuat: '.$e->getMessage());
            }
        }

        if (preg_match(self::CODE_FORMAT, $code) !== 1) {
            $findings[] = $this->error('EDF005', $source, "Error code {$code} tidak mengikuti format framework.");
        }

        if (isset($codes[$code])) {
            $findings[] = $this->error('EDF006', $source, "Duplicate error code {$code}; sudah digunakan oleh {$codes[$code]}.");
        } else {
            $codes[$code] = $source;
        }

        if ($definition === null) {
            return;
        }

        if (trim($definition->message) === '') {
            $findings[] = $this->error('EDF007', $source, 'Message tidak boleh kosong.');
        }

        if ($definition->httpStatus < 400 || $definition->httpStatus > 599) {
            $findings[] = $this->error('EDF008', $source, "HTTP status {$definition->httpStatus} berada di luar rentang 400–599.");
        }
    }

    private function error(string $ruleId, string $source, string $message): LintFinding
    {
        return new LintFinding($ruleId, LintLevel::ERROR, $source, $message);
    }
}
