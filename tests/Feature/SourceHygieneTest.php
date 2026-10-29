<?php

test('no emoji exists anywhere in application source files', function () {
    $directories = [
        app_path(),
        resource_path('views'),
        resource_path('js'),
        resource_path('css'),
        base_path('routes'),
        config_path(),
        database_path('seeders'),
        database_path('migrations'),
    ];

    $emojiRegex = '/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F700}-\x{1F77F}\x{1F780}-\x{1F7FF}\x{1F800}-\x{1F8FF}\x{1F900}-\x{1F9FF}\x{1FA00}-\x{1FA6F}\x{1FA70}-\x{1FAFF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u';

    $offendingFiles = [];

    foreach ($directories as $dir) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach ($files as $file) {
            if ($file->isDir()) {
                continue;
            }
            $ext = $file->getExtension();
            if (!in_array($ext, ['php', 'blade.php', 'js', 'css', 'json'])) {
                continue;
            }

            $content = file_get_contents($file->getPathname());
            if (preg_match($emojiRegex, $content)) {
                $offendingFiles[] = $file->getPathname();
            }
        }
    }

    expect($offendingFiles)->toBeEmpty('Emoji characters found in source files: ' . implode(', ', $offendingFiles));
});

test('no Indonesian UI terms exist in views and controllers', function () {
    $directories = [
        app_path('Http/Controllers'),
        resource_path('views'),
    ];

    $forbiddenWords = [
        'Cari',
        'Pesan',
        'Masuk',
        'Daftar',
        'Bayar',
        'Penyedia',
        'Pelanggan',
        'Kabupaten',
        'Layanan',
        'Pesanan',
    ];

    $offenses = [];

    foreach ($directories as $dir) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach ($files as $file) {
            if ($file->isDir()) {
                continue;
            }
            if (!str_ends_with($file->getFilename(), '.php')) {
                continue;
            }

            $content = file_get_contents($file->getPathname());
            foreach ($forbiddenWords as $word) {
                // Word boundary search
                if (preg_match('/\b' . preg_quote($word, '/') . '\b/i', $content)) {
                    $offenses[] = "{$file->getFilename()} contains '{$word}'";
                }
            }
        }
    }

    expect($offenses)->toBeEmpty('Indonesian words found: ' . implode(', ', $offenses));
});
