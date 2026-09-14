<?php
require __DIR__ . '/../private/bootstrap.php'; require_admin();
$pageTitle = 'Admin Dashboard — AfrikaFlix';
$count = (int)$pdo->query('SELECT COUNT(*) FROM movies')->fetchColumn();
$latest = $pdo->query('SELECT * FROM movies ORDER BY created_at DESC LIMIT 8')->fetchAll();
require __DIR__ . '/../partials/header.php';
?>
<div class="admin-top"><div><p class="eyebrow">CONTROL CENTER</p><h1>Welcome, <?= e($_SESSION['admin_username'] ?? 'Admin') ?></h1></div><a class="button ghost" href="../logout.php">Log out</a></div>
<div class="stats"><div><span>Total movies</span><strong><?= $count ?></strong></div></div>
<div class="section-head"><h2>Movie library</h2><a class="button" href="movies.php">Manage movies</a></div>
<div class="admin-list"><?php foreach ($latest as $m): ?><div><div><strong><?= e($m['title']) ?></strong><small><?= e($m['genre'] ?: 'Uncategorized') ?></small></div><a href="movies.php?edit=<?= (int)$m['id'] ?>">Edit</a></div><?php endforeach; ?></div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
