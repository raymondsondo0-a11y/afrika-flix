<?php
require __DIR__ . '/../private/bootstrap.php'; require_admin();
$id=(int)($_POST['id']??0); $title=trim($_POST['title']??'');
if ($title==='') exit('Title is required.');
$slug=trim($_POST['slug']??''); if($slug==='') $slug=strtolower(trim(preg_replace('/[^a-z0-9]+/i','-', $title),'-'));
$data=[$title,$slug,trim($_POST['description']??''),trim($_POST['genre']??''),($_POST['year']??'')!==''?(int)$_POST['year']:null,trim($_POST['poster_url']??''),trim($_POST['video_url']??''),(int)isset($_POST['featured'])];
if($id){$s=$pdo->prepare('UPDATE movies SET title=?,slug=?,description=?,genre=?,year=?,poster_url=?,video_url=?,featured=? WHERE id=?');$s->execute([...$data,$id]);}else{$s=$pdo->prepare('INSERT INTO movies (title,slug,description,genre,year,poster_url,video_url,featured) VALUES (?,?,?,?,?,?,?,?)');$s->execute($data);}
header('Location: movies.php'); exit;
