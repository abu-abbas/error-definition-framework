<?php

return [

    'logging' => [

        // Nama key khusus domain yang value-nya disamarkan pada log context.
        // Daftar ini menambah baseline package dan tidak dapat menghapusnya.
        'additional_sensitive_keys' => [],

    ],

    'generation' => [

        // Output generated TypeScript error codes. Arahkan ke workspace frontend
        // lain bila perlu; parent directory dibuat otomatis.
        'typescript_path' => resource_path('js/generated/error-codes.ts'),

    ],

];
