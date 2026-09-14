<?php
require __DIR__ . '/private/bootstrap.php';
if (is_admin()) { header('Location: admin/index.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = ? LIMIT 1');
    $stmt->execute([trim($_POST['username'] ?? '')]);
    $admin = $stmt->fetch();
    if ($admin && password_verify((string)($_POST['password'] ?? ''), $admin['password_hash'])) {
        session_regenerate_id(true); $_SESSION['admin_id'] = (int)$admin['id']; $_SESSION['admin_username'] = $admin['username'];
        header('Location: admin/index.php'); exit;
    }
    $error = 'Invalid username or password.';
}
$pageTitle = 'Admin Login — AfrikaFlix'; require __DIR__ . '/partials/header.php';
?>
<div class="auth-card"><p class="eyebrow">SECURE ACCESS</p><h1>Admin login</h1><?php if ($error): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?><form method="post"><label>Username<input name="username" autocomplete="username" required></label><label>Password<input name="password" type="password" autocomplete="current-password" required></label><button class="button" type="submit">Sign in</button></form></div>
<?php require __DIR__ . '/partials/footer.php'; ?>
