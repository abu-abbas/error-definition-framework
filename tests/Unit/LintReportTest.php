<?php

declare(strict_types=1);

namespace Edf\Tests\Unit;

use Edf\Core\Lint\LintFinding;
use Edf\Core\Lint\LintLevel;
use Edf\Core\Lint\LintReport;
use PHPUnit\Framework\TestCase;

final class LintReportTest extends TestCase
{
    public function test_empty_report_has_no_errors_or_warnings(): void
    {
        $report = new LintReport([]);

        $this->assertFalse($report->hasErrors());
        $this->assertFalse($report->hasWarnings());
    }

    public function test_levels_are_detected_independently(): void
    {
        $warning = new LintFinding('VAL007', LintLevel::WARNING, 'App\Request', 'Dinamis.');
        $error = new LintFinding('EDF003', LintLevel::ERROR, 'App\Error::CASE', 'Tidak ada definisi.');

        $this->assertFalse((new LintReport([$warning]))->hasErrors());
        $this->assertTrue((new LintReport([$warning]))->hasWarnings());
        $this->assertTrue((new LintReport([$warning, $error]))->hasErrors());
        $this->assertSame([$warning, $error], (new LintReport([$warning, $error]))->findings);
    }
}
