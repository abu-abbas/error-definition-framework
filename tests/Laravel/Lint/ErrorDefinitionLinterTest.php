<?php

declare(strict_types=1);

namespace Edf\Tests\Laravel\Lint;

use Edf\Core\Lint\LintFinding;
use Edf\Laravel\ErrorDefinitionServiceProvider;
use Edf\Laravel\Lint\ErrorDefinitionLinter;
use Edf\Tests\Fixtures\Lint\BrokenError;
use Edf\Tests\Fixtures\Lint\PlainRequest;
use Edf\Tests\Fixtures\Lint\ProblematicRequest;
use Edf\Tests\Fixtures\Lint\ThrowingRulesRequest;
use Edf\Tests\Fixtures\Validation\NonValidationDefinitionRequest;
use Edf\Tests\Fixtures\Validation\StoreSuratMasukRequest;
use Edf\Tests\Fixtures\Validation\SuratMasukValidationError;
use Edf\Tests\Fixtures\Validation\UnmappedRuleRequest;
use Orchestra\Testbench\TestCase;
use stdClass;

final class ErrorDefinitionLinterTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ErrorDefinitionServiceProvider::class];
    }

    public function test_valid_targets_produce_clean_report(): void
    {
        $report = $this->linter()->lint([SuratMasukValidationError::class], [StoreSuratMasukRequest::class]);

        $this->assertSame([], $report->findings);
        $this->assertFalse($report->hasErrors());
        $this->assertFalse($report->hasWarnings());
    }

    public function test_definition_and_validation_findings_are_combined(): void
    {
        $report = $this->linter()->lint([BrokenError::class], [UnmappedRuleRequest::class]);

        $this->assertSame(['EDF003', 'EDF004', 'EDF005', 'EDF007', 'EDF008', 'VAL005'], $this->ruleIds($report->findings));
        $this->assertSame(UnmappedRuleRequest::class.'[email.email]', $report->findings[5]->source);
    }

    public function test_validation_mapping_violations(): void
    {
        $report = $this->linter()->lint([], [
            ProblematicRequest::class,
            ThrowingRulesRequest::class,
            PlainRequest::class,
            stdClass::class,
            NonValidationDefinitionRequest::class,
        ]);

        $problematic = ProblematicRequest::class;

        $this->assertSame([
            ['VAL009', 'error', $problematic],
            ['VAL004', 'error', $problematic.'[tanggal.date]'],
            ['VAL003', 'error', $problematic.'[nama]'],
            ['VAL003', 'error', $problematic.'[nama.Required]'],
            ['VAL004', 'error', $problematic.'[catatan.string]'],
            ['VAL007', 'warning', $problematic],
            ['VAL007', 'warning', $problematic],
            ['VAL007', 'warning', $problematic],
            ['VAL006', 'warning', $problematic.'[alamat.required]'],
            ['VAL002', 'warning', ThrowingRulesRequest::class],
            ['VAL001', 'error', PlainRequest::class],
            ['VAL001', 'error', stdClass::class],
            ['VAL008', 'error', NonValidationDefinitionRequest::class.'[draft.required]'],
        ], array_map(
            fn (LintFinding $f): array => [$f->ruleId, $f->level->value, $f->source],
            $report->findings,
        ));

        $this->assertStringContainsString('tags.*', $report->findings[5]->message);
        $this->assertStringContainsString('jenis', $report->findings[6]->message);
        $this->assertStringContainsString('kode', $report->findings[7]->message);
    }

    public function test_requests_are_built_without_triggering_validation(): void
    {
        $this->linter()->lint([], [StoreSuratMasukRequest::class, UnmappedRuleRequest::class]);

        $this->addToAssertionCount(1);
    }

    private function linter(): ErrorDefinitionLinter
    {
        return $this->app->make(ErrorDefinitionLinter::class);
    }

    /**
     * @param list<LintFinding> $findings
     * @return list<string>
     */
    private function ruleIds(array $findings): array
    {
        return array_map(fn (LintFinding $f): string => $f->ruleId, $findings);
    }
}
