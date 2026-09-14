<?php
require __DIR__ . '/../private/bootstrap.php'; require_admin();

$id=(int)($_POST['id']??0);
if($id){
    $s=$pdo->prepare('SELECT poster_url, video_url FROM movies WHERE id=?');
    $s->execute([$id]);
    $movie=$s->fetch();

    $s=$pdo->prepare('DELETE FROM movies WHERE id=?');
    $s->execute([$id]);

    if($movie){
        foreach(['poster_url'=>'posters','video_url'=>'movies'] as $field=>$folder){
            $url=(string)($movie[$field]??'');
            if(!str_starts_with($url,'/uploads/'.$folder.'/')) continue;
            $file=realpath(__DIR__.'/..'.$url);
            $base=realpath(__DIR__.'/../uploads/'.$folder);
            if($file && $base && str_starts_with($file,$base.DIRECTORY_SEPARATOR) && is_file($file)) @unlink($file);
        }
    }
}
header('Location: movies.php'); exit;
