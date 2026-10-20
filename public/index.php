<?php

// LeadPulse CRM - Single Entry Point

declare(strict_types=1);

// 1. Minimal .env Parser (if .env file exists and getenv is empty)
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$key, $val] = explode('=', $line, 2);
            $key = trim($key);
            $val = trim($val);
            $val = trim($val, '"\'');
            if (getenv($key) === false) {
                putenv("{$key}={$val}");
                $_ENV[$key] = $val;
                $_SERVER[$key] = $val;
            }
        }
    }
}

// 2. PSR-4 Autoloader
$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
} else {
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        $baseDir = __DIR__ . '/../app/';

        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require_once $file;
        }
    });
}

// 3. Application Configuration & Error Handling
$appConfig = require __DIR__ . '/../config/app.php';
date_default_timezone_set($appConfig['timezone'] ?? 'Asia/Tashkent');

$isDebug = (bool)($appConfig['debug'] ?? false);

error_reporting(E_ALL);
ini_set('display_errors', $isDebug ? '1' : '0');

set_exception_handler(function (\Throwable $e) use ($isDebug) {
    // Log exception safely
    $logDir = __DIR__ . '/../storage/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    $logMessage = sprintf(
        "[%s] %s: %s in %s:%d\nStack trace:\n%s\n\n",
        date('Y-m-d H:i:s'),
        get_class($e),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        $e->getTraceAsString()
    );
    @file_put_contents($logDir . '/app.log', $logMessage, FILE_APPEND);

    http_response_code(500);

    if ($isDebug) {
        echo "<div style='font-family:sans-serif; background:#fee2e2; color:#991b1b; padding:24px; border-radius:8px; margin:20px;'>";
        echo "<h2 style='margin-top:0;'>Exception: " . htmlspecialchars($e->getMessage()) . "</h2>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " (Line " . $e->getLine() . ")</p>";
        echo "<pre style='background:#ffffff; padding:16px; border-radius:6px; overflow:auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        echo "</div>";
    } else {
        include __DIR__ . '/../app/Views/errors/500.php';
    }
    exit(1);
});

// 4. Session & Flash step
\App\Core\Session::start();
\App\Core\Session::stepFlash();

// 5. Load Routes
require_once __DIR__ . '/../routes/web.php';

// 6. Capture Request and Dispatch
$request = \App\Core\Request::capture();
$response = \App\Core\Router::dispatch($request);
$response->send();
