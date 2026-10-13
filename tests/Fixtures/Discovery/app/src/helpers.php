<?php

declare(strict_types=1);

// Helper ini sudah dimuat lewat autoload.files; discovery tidak boleh meng-include-nya ulang.
function edf_discovery_app_helper(): string
{
    return 'helper';
}
