<?php

declare(strict_types=1);

namespace Edf\Tests\Fixtures\Discovery;

use Composer\Autoload\ClassLoader;

/**
 * Meniru autoloader Composer milik aplikasi fixture: PSR-4, classmap, dan files.
 */
final class FixtureAutoloader
{
    private static bool $registered = false;

    public static function register(): void
    {
        if (self::$registered) {
            return;
        }

        self::$registered = true;

        $loader = new ClassLoader(__DIR__.'/app/vendor');
        $loader->addPsr4('EdfDiscoveryApp\\', __DIR__.'/app/src');
        $loader->addPsr4('EdfDiscoveryApp\\Tests\\', __DIR__.'/app/tests');
        $loader->addClassMap(['LegacyInvoiceError' => __DIR__.'/app/legacy/LegacyInvoiceError.php']);
        $loader->register();

        require_once __DIR__.'/app/src/helpers.php';

        $warning = new ClassLoader(__DIR__.'/warning/vendor');
        $warning->addPsr4('EdfDiscoveryWarning\\', __DIR__.'/warning/src');
        $warning->register();

        $failing = new ClassLoader(__DIR__.'/failing/vendor');
        $failing->addPsr4('EdfDiscoveryFailing\\', __DIR__.'/failing/src');
        $failing->register();
    }

    public static function path(string $project): string
    {
        return __DIR__.'/'.$project;
    }
}
