<?php

declare(strict_types=1);

namespace Edf\Core\Discovery;

use Composer\Autoload\ClassLoader;
use FilesystemIterator;
use JsonException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Membentuk candidate FQCN dari `autoload.psr-4` dan `autoload.classmap` root
 * composer.json. `autoload-dev` dan dependency `vendor/` tidak diproses.
 *
 * @see docs/decisions/1-error-definition-framework/1.9-error-definition-discovery-mechanism.md
 */
final class ComposerAutoloadCandidates
{
    private const string IDENTIFIER = '/^[A-Za-z_\x80-\xff][A-Za-z0-9_\x80-\xff]*$/';

    public function __construct(
        private readonly string $basePath,
    ) {}

    /**
     * @return array<class-string, string> FQCN => path file
     */
    public function find(): array
    {
        $autoload = $this->autoload();
        $candidates = [];

        foreach ($autoload['psr-4'] ?? [] as $prefix => $paths) {
            foreach ((array) $paths as $path) {
                $candidates += $this->psr4((string) $prefix, $this->resolve('psr-4', (string) $path));
            }
        }

        $classmapPaths = array_map(
            fn (mixed $path): string => $this->resolve('classmap', (string) $path),
            (array) ($autoload['classmap'] ?? []),
        );

        return $candidates + $this->classmap($classmapPaths);
    }

    /**
     * @return array<string, mixed>
     */
    private function autoload(): array
    {
        $file = $this->basePath.DIRECTORY_SEPARATOR.'composer.json';

        if (! is_file($file)) {
            throw DiscoveryException::composerJsonNotFound($file);
        }

        $contents = @file_get_contents($file);

        if ($contents === false) {
            throw DiscoveryException::invalidComposerJson($file, 'file tidak dapat dibaca');
        }

        try {
            $composer = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw DiscoveryException::invalidComposerJson($file, $e->getMessage());
        }

        if (! is_array($composer)) {
            throw DiscoveryException::invalidComposerJson($file, 'isi bukan object JSON');
        }

        $autoload = $composer['autoload'] ?? [];

        if (! is_array($autoload)) {
            throw DiscoveryException::invalidComposerJson($file, 'autoload bukan object');
        }

        return $autoload;
    }

    private function resolve(string $mapping, string $path): string
    {
        $resolved = realpath($this->basePath.DIRECTORY_SEPARATOR.$path);

        if ($resolved === false || ! is_readable($resolved)) {
            throw DiscoveryException::unreadablePath($mapping, $path);
        }

        return $resolved;
    }

    /**
     * @return array<class-string, string>
     */
    private function psr4(string $prefix, string $directory): array
    {
        if (! is_dir($directory)) {
            throw DiscoveryException::unreadablePath('psr-4', $directory);
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
        );
        $candidates = [];

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $relative = substr($file->getPathname(), strlen($directory) + 1, -4);
            $segments = explode('/', str_replace('\\', '/', $relative));

            if (array_filter($segments, fn (string $s): bool => preg_match(self::IDENTIFIER, $s) !== 1) !== []) {
                continue;
            }

            // File tanpa deklarasi type bernama sama (helpers, routes) tidak dimuat agar tidak di-include ulang.
            if (! $this->declaresType($file->getPathname(), end($segments))) {
                continue;
            }

            /** @var class-string $class */
            $class = $prefix.implode('\\', $segments);
            $candidates[$class] = $file->getPathname();
        }

        return $candidates;
    }

    /**
     * Mengambil class-to-file mapping Composer yang berada pada path classmap aplikasi.
     *
     * @param list<string> $paths
     * @return array<class-string, string>
     */
    private function classmap(array $paths): array
    {
        if ($paths === [] || ! class_exists(ClassLoader::class, false)) {
            return [];
        }

        $candidates = [];

        foreach (ClassLoader::getRegisteredLoaders() as $loader) {
            foreach ($loader->getClassMap() as $class => $file) {
                $file = realpath($file);

                if ($file !== false && $this->isWithin($file, $paths)) {
                    $candidates[$class] = $file;
                }
            }
        }

        return $candidates;
    }

    /**
     * @param list<string> $paths
     */
    private function isWithin(string $file, array $paths): bool
    {
        foreach ($paths as $path) {
            if ($file === $path || str_starts_with($file, $path.DIRECTORY_SEPARATOR)) {
                return true;
            }
        }

        return false;
    }

    private function declaresType(string $file, string $name): bool
    {
        $contents = @file_get_contents($file);

        if ($contents === false) {
            throw DiscoveryException::unreadablePath('psr-4', $file);
        }

        return preg_match('/\b(?:class|enum|interface|trait)\s+'.preg_quote($name, '/').'\b/i', $contents) === 1;
    }
}
