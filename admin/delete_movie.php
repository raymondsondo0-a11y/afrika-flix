<?php
require __DIR__ . '/../private/bootstrap.php'; require_admin();
$id=(int)($_POST['id']??0); if($id){$s=$pdo->prepare('DELETE FROM movies WHERE id=?');$s->execute([$id]);}
header('Location: movies.php'); exit;
