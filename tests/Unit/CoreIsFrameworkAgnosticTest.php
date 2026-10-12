<?php

declare(strict_types=1);

namespace Edf\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Core harus tetap PHP murni agar update Laravel hanya menyentuh adapter.
 */
final class CoreIsFrameworkAgnosticTest extends TestCase
{
    public function test_core_does_not_depend_on_laravel(): void
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(dirname(__DIR__, 2).'/src/Core', RecursiveDirectoryIterator::SKIP_DOTS),
        );

        foreach ($files as $file) {
            $this->assertDoesNotMatchRegularExpression(
                '/\bIlluminate\\\\|\bEdf\\\\Laravel\\\\/',
                file_get_contents($file->getPathname()),
                $file->getPathname().' tidak boleh bergantung pada Laravel.',
            );
        }
    }
}
