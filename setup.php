<?php

declare(strict_types=1);

$created = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = trim($_POST['db_host'] ?? '');
    $name = trim($_POST['db_name'] ?? '');
    $user = trim($_POST['db_user'] ?? '');
    $pass = (string)($_POST['db_pass'] ?? '');
    $adminUser = trim($_POST['admin_user'] ?? '');
    $adminPass = (string)($_POST['admin_pass'] ?? '');

    if (!$host || !$name || !$user || !$adminUser || strlen($adminPass) < 10) {
        $error = 'Fill all required fields. Admin password must be at least 10 characters.';
    } else {
        try {
            $pdo = new PDO(
                "mysql:host={$host};dbname={$name};charset=utf8mb4",
                $user,
                $pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(80) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $pdo->exec("CREATE TABLE IF NOT EXISTS movies (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(180) NOT NULL,
                slug VARCHAR(220) NOT NULL UNIQUE,
                description TEXT NULL,
                genre VARCHAR(120) NULL,
                year SMALLINT UNSIGNED NULL,
                poster_url VARCHAR(500) NULL,
                video_url VARCHAR(700) NULL,
                featured TINYINT(1) NOT NULL DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $hash = password_hash($adminPass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO admins (username, password_hash) VALUES (?, ?) ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)');
            $stmt->execute([$adminUser, $hash]);

            $config = "<?php\nreturn " . var_export([
                'db' => ['host' => $host, 'name' => $name, 'user' => $user, 'pass' => $pass, 'charset' => 'utf8mb4'],
                'app' => ['name' => 'AfrikaFlix', 'base_url' => 'https://shizer.kesug.com'],
            ], true) . ";\n";

            $privateDir = __DIR__ . '/private';
            if (!is_dir($privateDir)) mkdir($privateDir, 0755, true);
            if (file_put_contents($privateDir . '/config.php', $config, LOCK_EX) === false) {
                throw new RuntimeException('Could not write private/config.php. Check server permissions.');
            }
            $created = true;
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>AfrikaFlix Setup</title><link rel="stylesheet" href="assets/style.css"></head>
<body class="setup-page"><main class="setup-card">
<h1>AfrikaFlix Setup</h1>
<?php if ($created): ?>
<div class="notice success">Installation completed. Delete <b>setup.php</b> from the server before using the site.</div>
<a class="button" href="login.php">Open Admin Login</a>
<?php else: ?>
<p class="muted">Enter the database details supplied by InfinityFree. These credentials are written only to the server's private configuration file.</p>
<?php if ($error): ?><div class="notice error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<form method="post">
<label>Database host<input name="db_host" required></label>
<label>Database name<input name="db_name" required></label>
<label>Database username<input name="db_user" required></label>
<label>Database password<input name="db_pass" type="password"></label>
<label>Admin username<input name="admin_user" required></label>
<label>Admin password<input name="admin_pass" type="password" minlength="10" required></label>
<button class="button" type="submit">Install AfrikaFlix</button>
</form>
<?php endif; ?>
</main></body></html>
