<?php

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?PDO $instance = null;
    private static string $activeDriver = 'mysql';

    public static function connect(?array $config = null): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        if ($config === null) {
            $configPath = __DIR__ . '/../../config/database.php';
            $allConfig = file_exists($configPath) ? require $configPath : [];
            $default = $allConfig['default'] ?? 'sqlite';
            $config = $allConfig['connections'][$default] ?? [];
            self::$activeDriver = $default;
        }

        $driver = $config['driver'] ?? 'sqlite';

        try {
            if ($driver === 'sqlite') {
                $dbDir = dirname($config['database']);
                if (!is_dir($dbDir)) {
                    @mkdir($dbDir, 0755, true);
                }
                $dsn = 'sqlite:' . $config['database'];
                self::$instance = new PDO($dsn, null, null, $config['options'] ?? [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
                self::$instance->exec('PRAGMA foreign_keys = ON;');
                self::$activeDriver = 'sqlite';
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
                self::$activeDriver = 'mysql';
            }

            return self::$instance;
        } catch (PDOException $e) {
            error_log('Database Connection Warning: ' . $e->getMessage() . '. Fallback to SQLite.');
            
            // Auto fallback to SQLite if MySQL fails
            if ($driver === 'mysql') {
                $sqlitePath = __DIR__ . '/../../storage/database.sqlite';
                $dbDir = dirname($sqlitePath);
                if (!is_dir($dbDir)) {
                    @mkdir($dbDir, 0755, true);
                }
                self::$instance = new PDO('sqlite:' . $sqlitePath, null, null, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
                self::$instance->exec('PRAGMA foreign_keys = ON;');
                self::$activeDriver = 'sqlite';
                return self::$instance;
            }

            throw new RuntimeException('Maʼlumotlar bazasiga ulanishda xatolik: ' . $e->getMessage());
        }
    }

    public static function getDriver(): string
    {
        return self::$activeDriver;
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
