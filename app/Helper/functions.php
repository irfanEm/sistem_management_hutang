<?php

use Dotenv\Dotenv;

// app/Helper/functions.php

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        // Ambil base URL dari konfigurasi
        $baseUrl = $_ENV['BASE_URL'];
        
        // Hilangkan slash di depan path jika ada
        $path = ltrim($path, '/');
        
        return "{$baseUrl}/{$path}";
    }
}