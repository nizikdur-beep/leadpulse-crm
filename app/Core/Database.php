<?php

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?PDO $instance = null;

    public static function connect(?array $config = null): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        if ($config === null) {
            $configPath = __DIR__ . '/../../config/database.php';
            $allConfig = file_exists($configPath) ? require $configPath : [];
            $default = $allConfig['default'] ?? 'mysql';
            $config = $allConfig['connections'][$default] ?? [];
        }

        $driver = $config['driver'] ?? 'mysql';

        try {
            if ($driver === 'sqlite') {
                $dsn = 'sqlite:' . $config['database'];
                self::$instance = new PDO($dsn, null, null, $config['options'] ?? [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
                self::$instance->exec('PRAGMA foreign_keys = ON;');
            } else {
                $dsn = sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                    $config['host'] ?? '127.0.0.1',
                    $config['port'] ?? '3306',
                    $config['database'] ?? 'leadpulse',
                    $config['charset'] ?? 'utf8mb4'
                );

                $options = $config['options'] ?? [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];

                self::$instance = new PDO(
                    $dsn,
                    $config['username'] ?? 'root',
                    $config['password'] ?? '',
                    $options
                );
            }

            return self::$instance;
        } catch (PDOException $e) {
            // Log the error securely without leaking credentials
            error_log('Database Connection Error: ' . $e->getMessage());
            throw new RuntimeException('Maʼlumotlar bazasiga ulanishda xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    public static function setConnection(PDO $pdo): void
    {
        self::$instance = $pdo;
    }

    public static function disconnect(): void
    {
        self::$instance = null;
    }
}
