<?php

namespace App;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private static ?PDO $pdo = null;

    public static function get(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $_ENV['DB_HOST'] ?? '127.0.0.1',
            $_ENV['DB_PORT'] ?? '3306',
            $_ENV['DB_NAME'] ?? 'books_api',
            $_ENV['DB_CHARSET'] ?? 'utf8mb4'
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,

            // Enable SSL for TiDB Cloud
            PDO::MYSQL_ATTR_SSL_CA       => __DIR__ . '/../certs/isrgrootx1.pem',
        ];

        try {
            self::$pdo = new PDO(
                $dsn,
                $_ENV['DB_USER'] ?? 'root',
                $_ENV['DB_PASS'] ?? '',
                $options
            );
        } catch (PDOException $e) {
            error_log('[DB] ' . $e->getMessage());
            throw new RuntimeException('Database connection failed', 500, $e);
        }

        return self::$pdo;
    }
}