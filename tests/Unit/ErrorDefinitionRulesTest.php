<?php

declare(strict_types=1);

namespace Edf\Tests\Unit;

use Edf\Core\Lint\ErrorDefinitionRules;
use Edf\Core\Lint\LintFinding;
use Edf\Core\Lint\LintLevel;
use Edf\Tests\Fixtures\Lint\BrokenError;
use Edf\Tests\Fixtures\Lint\IntBackedError;
use Edf\Tests\Fixtures\Lint\PlainEnum;
use Edf\Tests\Fixtures\SuratMasukError;
use Edf\Tests\Fixtures\Validation\SuratMasukValidationError;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ErrorDefinitionRulesTest extends TestCase
{
    public function test_valid_enum_has_no_findings(): void
    {
        $this->assertSame([], (new ErrorDefinitionRules())->check([SuratMasukValidationError::class]));
    }

    public function test_all_violations_are_collected(): void
    {
        $findings = (new ErrorDefinitionRules())->check([
            BrokenError::class,
            SuratMasukError::class,
            IntBackedError::class,
            PlainEnum::class,
            'App\Missing',
            stdClass::class,
        ]);

        $this->assertSame([
            ['EDF003', BrokenError::class.'::MISSING_DEFINITION'],
            ['EDF004', BrokenError::class.'::DOUBLE_DEFINITION'],
            ['EDF005', BrokenError::class.'::BAD_CODE'],
            ['EDF007', BrokenError::class.'::EMPTY_MESSAGE'],
            ['EDF008', BrokenError::class.'::BAD_STATUS'],
            ['EDF006', SuratMasukError::class.'::DRAFT_LOCKED'],
            ['EDF003', SuratMasukError::class.'::UNDEFINED'],
            ['EDF002', IntBackedError::class],
            ['EDF001', PlainEnum::class],
            ['EDF001', 'App\Missing'],
            ['EDF001', stdClass::class],
        ], array_map(fn (LintFinding $f): array => [$f->ruleId, $f->source], $findings));

        foreach ($findings as $finding) {
            $this->assertSame(LintLevel::ERROR, $finding->level);
        }
    }

    public function test_duplicate_code_names_first_owner(): void
    {
        $findings = (new ErrorDefinitionRules())->check([SuratMasukValidationError::class, SuratMasukError::class]);

        $this->assertSame('EDF006', $findings[0]->ruleId);
        $this->assertSame(SuratMasukError::class.'::NOMOR_SURAT_REQUIRED', $findings[0]->source);
        $this->assertStringContainsString(SuratMasukValidationError::class.'::NOMOR_SURAT_REQUIRED', $findings[0]->message);
    }

    public function test_same_target_twice_is_checked_once(): void
    {
        $rules = new ErrorDefinitionRules();

        $this->assertSame([], $rules->check([SuratMasukValidationError::class, SuratMasukValidationError::class]));
    }

    public function test_code_format(): void
    {
        foreach (['SM-WF-001' => true, 'IAM-AUTH-0001' => true, 'draft_locked' => false, 'SM-01' => false, 'SM-001' => false, 'sm-wf-001' => false] as $code => $valid) {
            $this->assertSame($valid, preg_match(ErrorDefinitionRules::CODE_FORMAT, $code) === 1, $code);
        }
    }
}
