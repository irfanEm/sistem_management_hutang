<?php

namespace IRFANM\SIMAHU\Config;

use PDO;

class Database
{
    private static ?PDO $pdo = null;

    public static function getConn(string $env = 'test'): PDO
    {
        if (self::$pdo === null) {
            require_once __DIR__ . '/../../config/database.php';
            $config = getDatabaseConfig();
            $dbConfig = $config["database"][$env];

            self::$pdo = new PDO(
                $dbConfig["url"],
                $dbConfig["username"],
                $dbConfig["password"],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        }

        return self::$pdo;
    }

    public static function beginTransaction()
    {
        self::$pdo->beginTransaction();
    }

    public static function commitTransaction()
    {
        self::$pdo->commit();
    }

    public static function rollbackTransaction()
    {
        self::$pdo->rollBack();
    }
}
