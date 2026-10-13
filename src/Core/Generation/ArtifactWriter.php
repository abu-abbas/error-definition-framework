<?php

declare(strict_types=1);

namespace Edf\Core\Generation;

/**
 * Menulis generated artifact hanya ketika content berubah, melalui temporary
 * file pada directory yang sama lalu atomic rename.
 */
final class ArtifactWriter
{
    public function isUpToDate(string $path, string $content): bool
    {
        return is_file($path) && @file_get_contents($path) === $content;
    }

    /**
     * @return bool true jika file ditulis, false jika content sudah sama
     */
    public function write(string $path, string $content): bool
    {
        if ($this->isUpToDate($path, $content)) {
            return false;
        }

        $directory = dirname($path);

        if (! is_dir($directory) && ! @mkdir($directory, 0777, true) && ! is_dir($directory)) {
            throw GenerationException::unwritable($path, 'parent directory tidak dapat dibuat');
        }

        $temporary = @tempnam($directory, '.'.basename($path).'.');

        // tempnam() jatuh ke system temp dir bila gagal; rename lintas filesystem tidak atomic.
        if ($temporary === false || realpath(dirname($temporary)) !== realpath($directory)) {
            if ($temporary !== false) {
                @unlink($temporary);
            }

            throw GenerationException::unwritable($path, 'temporary file tidak dapat dibuat');
        }

        if (@file_put_contents($temporary, $content) !== strlen($content)) {
            @unlink($temporary);

            throw GenerationException::unwritable($path, 'temporary file tidak dapat ditulis');
        }

        @chmod($temporary, 0666 & ~umask());

        if (! @rename($temporary, $path)) {
            @unlink($temporary);

            throw GenerationException::unwritable($path, 'file tidak dapat dipindahkan ke target');
        }

        return true;
    }
}
