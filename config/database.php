<?php

use Dotenv\Dotenv;

function getDatabaseConfig(): array
{
    // Load konfigurasi dari .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();

    return [
        "database" => [
            "test" => [
                "url" => sprintf(
                    "mysql:host=%s:%s;dbname=%s",
                    $_ENV['DB_TEST_HOST'] ?? '127.0.0.1',
                    $_ENV['DB_TEST_PORT'] ?? '3306',
                    $_ENV['DB_TEST_NAME'] ?? 'siashaf_test'
                ),
                "username" => $_ENV['DB_TEST_USER'] ?? 'root',
                "password" => $_ENV['DB_TEST_PASSWORD'] ?? '',
            ],
            "prod" => [
                "url" => sprintf(
                    "mysql:host=%s:%s;dbname=%s",
                    $_ENV['DB_PROD_HOST'] ?? '127.0.0.1',
                    $_ENV['DB_PROD_PORT'] ?? '3306',
                    $_ENV['DB_PROD_NAME'] ?? 'siashaf'
                ),
                "username" => $_ENV['DB_PROD_USER'] ?? 'root',
                "password" => $_ENV['DB_PROD_PASSWORD'] ?? '',
            ]
        ]
    ];
}
