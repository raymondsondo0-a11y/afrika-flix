<?php
require __DIR__ . '/private/bootstrap.php';
$pageTitle = 'AfrikaFlix — African Movies';
$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $stmt = $pdo->prepare('SELECT * FROM movies WHERE title LIKE ? OR genre LIKE ? ORDER BY created_at DESC');
    $like = '%' . $q . '%'; $stmt->execute([$like, $like]); $movies = $stmt->fetchAll();
} else {
    $movies = $pdo->query('SELECT * FROM movies ORDER BY featured DESC, created_at DESC')->fetchAll();
}
$featured = array_values(array_filter($movies, fn($m) => (int)$m['featured'] === 1));
require __DIR__ . '/partials/header.php';
?>
<section class="hero"><div><p class="eyebrow">AFRICAN ENTERTAINMENT</p><h1>Stories from Africa.<br><span>Made for everyone.</span></h1><p>Discover movies, stories and entertainment in one clean, fast library.</p><form class="search" method="get"><input name="q" value="<?= e($q) ?>" placeholder="Search movies or genres..."><button>Search</button></form></div></section>
<section class="section-head"><h2><?= $q !== '' ? 'Search results' : 'Latest movies' ?></h2><span><?= count($movies) ?> titles</span></section>
<div class="movie-grid">
<?php foreach ($movies as $movie): ?><article class="movie-card"><a href="watch.php?id=<?= (int)$movie['id'] ?>"><div class="poster"><?php if (!empty($movie['poster_url'])): ?><img src="<?= e($movie['poster_url']) ?>" alt="<?= e($movie['title']) ?>"><?php else: ?><span><?= e(strtoupper(substr($movie['title'],0,1))) ?></span><?php endif; ?></div><div class="movie-info"><h3><?= e($movie['title']) ?></h3><p><?= e($movie['genre'] ?: 'African film') ?><?= $movie['year'] ? ' • '.(int)$movie['year'] : '' ?></p></div></a></article><?php endforeach; ?>
</div>
<?php if (!$movies): ?><div class="empty"><h2>No movies yet</h2><p>Log in to the admin area and add your first title.</p></div><?php endif; ?>
<?php require __DIR__ . '/partials/footer.php'; ?>
