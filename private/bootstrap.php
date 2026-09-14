<?php

declare(strict_types=1);

$configFile = __DIR__ . '/config.php';
if (!is_file($configFile)) {
    http_response_code(503);
    exit('AfrikaFlix is not installed yet. Open /setup.php first.');
}

$config = require $configFile;
$db = $config['db'];

try {
    $pdo = new PDO(
        'mysql:host=' . $db['host'] . ';dbname=' . $db['name'] . ';charset=' . ($db['charset'] ?? 'utf8mb4'),
        $db['user'],
        $db['pass'],
        [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]
    );
} catch (Throwable $e) {
    http_response_code(503); exit('Database connection failed. Check private/config.php.');
}

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
function e(?string $value): string { return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'); }
function is_admin(): bool { return !empty($_SESSION['admin_id']); }
function require_admin(): void { global $config; if (!is_admin()) { header('Location: ' . rtrim($config['app']['base_url'] ?? '', '/') . '/login.php'); exit; } }
function app_name(): string { global $config; return $config['app']['name'] ?? 'AfrikaFlix'; }
