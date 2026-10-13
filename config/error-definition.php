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

        // Output generated JSON catalog untuk tooling lokal.
        'catalog_path' => storage_path('app/error-definition/error-catalog.json'),

    ],

    'publish' => [

        // Tujuan `error-definition:publish`: 'disk' atau 'http'.
        'driver' => env('EDF_PUBLISH_DRIVER', 'disk'),

        // Identitas tetap aplikasi berupa slug huruf kecil, misalnya 'surat-masuk'.
        // Sengaja tidak diambil dari APP_NAME karena nama tampilan dapat berubah.
        'application' => env('EDF_PUBLISH_APPLICATION'),

        // Driver 'disk': filesystem disk Laravel (local, s3, sftp, ...) dan path
        // file. {application} diganti identitas aplikasi; file ditimpa setiap publish.
        'disk' => [
            'name' => env('EDF_PUBLISH_DISK', 'local'),
            'path' => 'error-definition/{application}.json',
        ],

        // Driver 'http': POST payload JSON ke URL ini. Token, jika diisi, dikirim
        // sebagai bearer token. Timeout dalam detik; tidak ada retry otomatis.
        'http' => [
            'url' => env('EDF_PUBLISH_URL'),
            'token' => env('EDF_PUBLISH_TOKEN'),
            'timeout' => 10,
        ],

    ],

];
