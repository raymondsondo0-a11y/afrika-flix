<?php
require __DIR__ . '/private/bootstrap.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM movies WHERE id = ?');
$stmt->execute([$id]);
$movie = $stmt->fetch();
if (!$movie) { http_response_code(404); exit('Movie not found.'); }
$pageTitle = $movie['title'] . ' — AfrikaFlix';
require __DIR__ . '/partials/header.php';
?>
<div class="watch-layout"><section><div class="video-box"><?php if (!empty($movie['video_url'])): ?><video controls preload="metadata" poster="<?= e($movie['poster_url']) ?>"><source src="<?= e($movie['video_url']) ?>"></video><?php else: ?><div class="video-empty">Video not available yet.</div><?php endif; ?></div></section><aside class="movie-detail"><p class="eyebrow">NOW PLAYING</p><h1><?= e($movie['title']) ?></h1><p class="meta"><?= e($movie['genre'] ?: 'African film') ?><?= $movie['year'] ? ' • '.(int)$movie['year'] : '' ?></p><p><?= nl2br(e($movie['description'] ?: 'No description available.')) ?></p><a class="button" href="index.php">Back to library</a></aside></div>
<?php require __DIR__ . '/partials/footer.php'; ?>
