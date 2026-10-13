<?php

return [

    'logging' => [

        // Nama key khusus domain yang value-nya disamarkan pada log context.
        // Daftar ini menambah baseline package (password, token, api_key, dst.)
        // dan tidak dapat menghapusnya. Pencocokan mengabaikan huruf besar/kecil
        // serta pemisah, jadi 'no_rekening' juga cocok dengan 'noRekening'.
        //
        // Contoh:
        //     'additional_sensitive_keys' => ['nik', 'no_rekening', 'nomor_hp'],
        //
        //     context ['nik' => '3171...', 'nama' => 'Budi'] tercatat sebagai
        //     ['nik' => '[REDACTED]', 'nama' => 'Budi'].
        'additional_sensitive_keys' => [],

    ],

    'generation' => [

        // Output generated TypeScript error codes. Arahkan ke workspace frontend
        // lain bila perlu; parent directory dibuat otomatis.
        'typescript_path' => resource_path('js/generated/error-codes.ts'),

        // Output generated JSON catalog untuk tooling dan publication registry.
        'catalog_path' => storage_path('app/error-definition/error-catalog.json'),

    ],

];
