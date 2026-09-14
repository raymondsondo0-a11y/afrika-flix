<?php
require __DIR__ . '/../private/bootstrap.php'; require_admin();

$editId = (int)($_GET['edit'] ?? 0);
$movie = ['id'=>0,'title'=>'','slug'=>'','description'=>'','genre'=>'','year'=>'','poster_url'=>'','video_url'=>'','featured'=>0];
if ($editId) {
    $s=$pdo->prepare('SELECT * FROM movies WHERE id=?');
    $s->execute([$editId]);
    $movie=$s->fetch() ?: $movie;
}
$movies = $pdo->query('SELECT * FROM movies ORDER BY created_at DESC')->fetchAll();
$pageTitle = 'Manage Movies — AfrikaFlix'; require __DIR__ . '/../partials/header.php';
?>
<div class="admin-top">
    <div><p class="eyebrow">LIBRARY</p><h1><?= $editId ? 'Edit movie' : 'Add movie' ?></h1></div>
    <a class="button ghost" href="index.php">Dashboard</a>
</div>

<form class="movie-form" method="post" action="save_movie.php" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= (int)$movie['id'] ?>">

    <label>Title
        <input name="title" required value="<?= e($movie['title']) ?>">
    </label>

    <label>Slug
        <input name="slug" value="<?= e($movie['slug']) ?>" placeholder="optional-auto-generated">
    </label>

    <div class="form-row">
        <label>Genre<input name="genre" value="<?= e($movie['genre']) ?>"></label>
        <label>Year<input name="year" type="number" min="1900" max="2100" value="<?= e((string)$movie['year']) ?>"></label>
    </div>

    <label>Poster — Upload from PC
        <input name="poster_file" type="file" accept="image/jpeg,image/png,image/webp">
        <small>Option 1: Choose a poster image from your computer.</small>
    </label>

    <label>OR Poster URL
        <input name="poster_url" type="url" value="<?= e($movie['poster_url']) ?>" placeholder="https://...">
        <small>Option 2: Paste an online poster image URL.</small>
    </label>

    <label>Movie — Upload from PC
        <input name="video_file" type="file" accept="video/mp4,video/webm,video/quicktime">
        <small>Option 1: Choose the movie file directly from your computer.</small>
    </label>

    <label>OR Movie URL
        <input name="video_url" type="url" value="<?= e($movie['video_url']) ?>" placeholder="https://.../movie.mp4">
        <small>Option 2: Paste a direct online movie URL.</small>
    </label>

    <?php if (!empty($movie['poster_url']) || !empty($movie['video_url'])): ?>
        <div class="upload-note">
            <strong>Existing media</strong>
            <?php if (!empty($movie['poster_url'])): ?><div>Poster: <?= e($movie['poster_url']) ?></div><?php endif; ?>
            <?php if (!empty($movie['video_url'])): ?><div>Movie: <?= e($movie['video_url']) ?></div><?php endif; ?>
            <small>When editing, leave an upload field empty to keep the current media. Uploading a replacement will replace the old local file.</small>
        </div>
    <?php endif; ?>

    <label>Description<textarea name="description" rows="6"><?= e($movie['description']) ?></textarea></label>
    <label class="check"><input type="checkbox" name="featured" value="1" <?= $movie['featured'] ? 'checked' : '' ?>> Featured movie</label>

    <button class="button" type="submit">Save movie</button>
</form>

<div class="section-head"><h2>All movies</h2><span><?= count($movies) ?></span></div>
<div class="admin-list">
<?php foreach ($movies as $m): ?>
    <div>
        <div><strong><?= e($m['title']) ?></strong><small><?= e($m['genre'] ?: 'Uncategorized') ?></small></div>
        <span class="actions">
            <a href="movies.php?edit=<?= (int)$m['id'] ?>">Edit</a>
            <form method="post" action="delete_movie.php" onsubmit="return confirm('Delete this movie and its uploaded media?')">
                <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                <button>Delete</button>
            </form>
        </span>
    </div>
<?php endforeach; ?>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
