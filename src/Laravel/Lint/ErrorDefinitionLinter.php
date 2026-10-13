<?php

declare(strict_types=1);

namespace Edf\Laravel\Lint;

use BackedEnum;
use Edf\Core\ErrorCode;
use Edf\Core\Lint\ErrorDefinitionRules;
use Edf\Core\Lint\LintReport;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Mengaudit enum error dan FormRequest yang diberikan, lalu mengembalikan satu
 * report. Linter tidak melakukan discovery dan tidak berhenti pada temuan pertama.
 *
 * @see docs/reference/error-definition-linter.md
 */
final class ErrorDefinitionLinter
{
    public function __construct(
        private readonly ErrorDefinitionRules $errorDefinitionRules,
        private readonly ValidationMappingRules $validationMappingRules,
    ) {}

    /**
     * @param iterable<class-string<ErrorCode&BackedEnum>> $errorEnums
     * @param iterable<class-string<FormRequest>> $formRequests
     */
    public function lint(iterable $errorEnums, iterable $formRequests = []): LintReport
    {
        return new LintReport([
            ...$this->errorDefinitionRules->check($errorEnums),
            ...$this->validationMappingRules->check($formRequests),
        ]);
    }
}
