<?php

declare(strict_types=1);

namespace Edf\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Skill dan guideline dibaca agent apa adanya, jadi nama class di dalamnya harus tetap valid.
 */
final class AgentSkillTest extends TestCase
{
    private const BOOST_PATH = __DIR__.'/../../resources/boost';

    public function test_every_skill_has_frontmatter_matching_its_directory(): void
    {
        $skills = glob(self::BOOST_PATH.'/skills/*/SKILL.md');

        $this->assertNotEmpty($skills);

        foreach ($skills as $skill) {
            $this->assertMatchesRegularExpression('/\A---\n.*?^name: (\S+)$.*?^description: ".+"$.*?^---$/ms', file_get_contents($skill), $skill);
            preg_match('/^name: (\S+)$/m', file_get_contents($skill), $name);
            $this->assertSame(basename(dirname($skill)), $name[1], $skill);
        }
    }

    public function test_referenced_package_types_exist(): void
    {
        $files = [...glob(self::BOOST_PATH.'/skills/*/SKILL.md'), ...glob(self::BOOST_PATH.'/guidelines/*.blade.php')];
        $types = [];

        foreach ($files as $file) {
            preg_match_all('/Edf(?:\\\\{1,2}[A-Z]\w*)+/', file_get_contents($file), $matches);

            foreach ($matches[0] as $type) {
                $types[str_replace('\\\\', '\\', $type)] = $file;
            }
        }

        $this->assertNotEmpty($types);

        foreach ($types as $type => $file) {
            $this->assertTrue(
                class_exists($type) || interface_exists($type) || trait_exists($type) || enum_exists($type),
                $file.' menyebut '.$type.' yang tidak ada.',
            );
        }
    }
}
