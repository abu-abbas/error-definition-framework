<?php

declare(strict_types=1);

namespace Edf\Tests\Unit;

use Edf\Core\Generation\ArtifactWriter;
use PHPUnit\Framework\TestCase;

final class ArtifactWriterTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir().'/edf-writer-'.bin2hex(random_bytes(4));
    }

    protected function tearDown(): void
    {
        exec('rm -rf '.escapeshellarg($this->directory));
    }

    public function test_creates_parent_directory_and_writes_content(): void
    {
        $path = $this->directory.'/nested/error-codes.ts';

        $this->assertTrue((new ArtifactWriter())->write($path, "isi\n"));
        $this->assertSame("isi\n", file_get_contents($path));
        $this->assertSame(['error-codes.ts'], array_values(array_diff(scandir(dirname($path)), ['.', '..'])));
    }

    public function test_unchanged_content_is_not_rewritten(): void
    {
        $path = $this->directory.'/error-codes.ts';
        $writer = new ArtifactWriter();
        $writer->write($path, "isi\n");
        touch($path, 1_000_000);
        clearstatcache();

        $this->assertFalse($writer->write($path, "isi\n"));
        clearstatcache();
        $this->assertSame(1_000_000, filemtime($path));

        $this->assertTrue($writer->write($path, "baru\n"));
        $this->assertSame("baru\n", file_get_contents($path));
    }

    public function test_is_up_to_date(): void
    {
        $path = $this->directory.'/error-codes.ts';
        $writer = new ArtifactWriter();

        $this->assertFalse($writer->isUpToDate($path, "isi\n"));
        $writer->write($path, "isi\n");
        $this->assertTrue($writer->isUpToDate($path, "isi\n"));
        $this->assertFalse($writer->isUpToDate($path, "lain\n"));
    }
}
