<?php

// CLI Migration Runner for LeadPulse Mini Framework
require_once __DIR__ . '/../../app/Core/Database.php';

use App\Core\Database;

echo "=== LeadPulse Database Migration ===\n";

// Load configuration
$config = require __DIR__ . '/../../config/database.php';
$default = $config['default'] ?? 'mysql';
$dbConfig = $config['connections'][$default] ?? [];

try {
    if ($default === 'mysql') {
        // First, check/create the database if it doesn't exist
        $pdoRoot = new PDO(
            sprintf('mysql:host=%s;port=%s;charset=%s', $dbConfig['host'] ?? '127.0.0.1', $dbConfig['port'] ?? '3306', $dbConfig['charset'] ?? 'utf8mb4'),
            $dbConfig['username'] ?? 'root',
            $dbConfig['password'] ?? '',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $dbName = $dbConfig['database'] ?? 'leadpulse';
        $pdoRoot->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        echo "[OK] Maʼlumotlar bazasi tekshirildi/yaratildi: {$dbName}\n";
    }

    $db = Database::connect($dbConfig);
    echo "[OK] Baza bilan ulanish muvaffaqiyatli oʻrnatildi.\n";

    if ($default === 'mysql') {
        $sql = file_get_contents(__DIR__ . '/001_create_tables.sql');
        $db->exec($sql);
        echo "[OK] MySQL jadvallari muvaffaqiyatli yaratildi/yangilandi.\n";
    } else {
        // SQLite support for standalone zero-config tests
        $db->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                role TEXT NOT NULL DEFAULT 'manager',
                is_active INTEGER NOT NULL DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS clients (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                phone TEXT,
                email TEXT,
                company TEXT,
                address TEXT,
                notes TEXT,
                created_by INTEGER NOT NULL,
                assigned_to INTEGER NOT NULL,
                deleted_at DATETIME NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS leads (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                client_id INTEGER NOT NULL,
                user_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                amount NUMERIC NOT NULL DEFAULT 0,
                stage TEXT NOT NULL DEFAULT 'new',
                source TEXT,
                notes TEXT,
                next_contact_at DATE,
                lost_reason TEXT,
                deleted_at DATETIME NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS activities (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                lead_id INTEGER,
                client_id INTEGER,
                user_id INTEGER NOT NULL,
                type TEXT NOT NULL DEFAULT 'note',
                note TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS tasks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                lead_id INTEGER,
                client_id INTEGER,
                user_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                description TEXT,
                due_date DATE NOT NULL,
                priority TEXT NOT NULL DEFAULT 'medium',
                status TEXT NOT NULL DEFAULT 'open',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS lead_stage_history (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                lead_id INTEGER NOT NULL,
                user_id INTEGER NOT NULL,
                old_stage TEXT NOT NULL,
                new_stage TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS login_attempts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                ip_address TEXT NOT NULL,
                email TEXT NOT NULL,
                attempts INTEGER NOT NULL DEFAULT 1,
                last_attempt_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                UNIQUE (ip_address, email)
            );
        ");
        echo "[OK] SQLite jadvallari yaratildi.\n";
    }

    echo "=== Migratsiya muvaffaqiyatli yakunlandi! ===\n";
} catch (\Throwable $e) {
    echo "[XATO] Migratsiyada xatolik yuz berdi: " . $e->getMessage() . "\n";
    exit(1);
}
