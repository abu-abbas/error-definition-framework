<?php

declare(strict_types=1);

namespace Edf\Laravel;

use Edf\Core\ErrorDefinitionReader;
use Illuminate\Support\ServiceProvider;

final class ErrorDefinitionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Singleton agar cache Reader digunakan ulang selama lifecycle process (Decision 1.3.1).
        $this->app->singleton(ErrorDefinitionReader::class);
    }
}
