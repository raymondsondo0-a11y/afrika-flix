<?php
require __DIR__ . '/../private/bootstrap.php'; require_admin();

$id=(int)($_POST['id']??0);
$title=trim($_POST['title']??'');
if ($title==='') exit('Title is required.');

$slug=trim($_POST['slug']??'');
if($slug==='') $slug=strtolower(trim(preg_replace('/[^a-z0-9]+/i','-', $title),'-'));

$description=trim($_POST['description']??'');
$genre=trim($_POST['genre']??'');
$year=($_POST['year']??'')!==''?(int)$_POST['year']:null;
$posterUrl=trim($_POST['poster_url']??'');
$videoUrl=trim($_POST['video_url']??'');
$featured=(int)isset($_POST['featured']);

$oldPoster='';
$oldVideo='';
if($id){
    $existing=$pdo->prepare('SELECT poster_url, video_url FROM movies WHERE id=?');
    $existing->execute([$id]);
    $old=$existing->fetch();
    if(!$old) exit('Movie not found.');
    $oldPoster=(string)($old['poster_url']??'');
    $oldVideo=(string)($old['video_url']??'');
    if($posterUrl==='') $posterUrl=$oldPoster;
    if($videoUrl==='') $videoUrl=$oldVideo;
}

$createdFiles=[];
$maxPosterBytes=5*1024*1024;
$maxVideoBytes=500*1024*1024;

function fail_upload($message, $createdFiles=[]){
    foreach($createdFiles as $file){ if(is_file($file)) @unlink($file); }
    http_response_code(400);
    exit($message);
}

function save_uploaded_media($field, $directory, $allowedMime, $maxBytes, $label, &$createdFiles){
    if(!isset($_FILES[$field]) || $_FILES[$field]['error']===UPLOAD_ERR_NO_FILE) return null;
    $file=$_FILES[$field];
    if($file['error']!==UPLOAD_ERR_OK) fail_upload($label.' upload failed. PHP upload error: '.$file['error'], $createdFiles);
    if((int)$file['size']<=0) fail_upload($label.' file is empty.', $createdFiles);
    if((int)$file['size']>$maxBytes) fail_upload($label.' is too large for the application limit.', $createdFiles);
    if(!is_uploaded_file($file['tmp_name'])) fail_upload('Invalid '.$label.' upload.', $createdFiles);

    $finfo=new finfo(FILEINFO_MIME_TYPE);
    $mime=$finfo->file($file['tmp_name']);
    if(!isset($allowedMime[$mime])) fail_upload('Invalid '.$label.' type. Allowed: '.implode(', ', array_values($allowedMime)).'.', $createdFiles);

    $extension=$allowedMime[$mime];
    $name=bin2hex(random_bytes(16)).'.'.$extension;
    if(!is_dir($directory) && !mkdir($directory,0755,true)) fail_upload('Could not create '.$label.' upload directory.', $createdFiles);
    $target=$directory.'/'.$name;
    if(!move_uploaded_file($file['tmp_name'],$target)) fail_upload('Could not save the uploaded '.$label.'.', $createdFiles);
    $createdFiles[]=$target;
    return $name;
}

$posterName=save_uploaded_media(
    'poster_file',
    __DIR__.'/../uploads/posters',
    ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'],
    $maxPosterBytes,
    'Poster',
    $createdFiles
);
if($posterName!==null) $posterUrl='/uploads/posters/'.$posterName;

$videoName=save_uploaded_media(
    'video_file',
    __DIR__.'/../uploads/movies',
    ['video/mp4'=>'mp4','video/webm'=>'webm','video/quicktime'=>'mov'],
    $maxVideoBytes,
    'Movie',
    $createdFiles
);
if($videoName!==null) $videoUrl='/uploads/movies/'.$videoName;

$data=[$title,$slug,$description,$genre,$year,$posterUrl,$videoUrl,$featured];

try {
    if($id){
        $s=$pdo->prepare('UPDATE movies SET title=?,slug=?,description=?,genre=?,year=?,poster_url=?,video_url=?,featured=? WHERE id=?');
        $s->execute([...$data,$id]);
    }else{
        $s=$pdo->prepare('INSERT INTO movies (title,slug,description,genre,year,poster_url,video_url,featured) VALUES (?,?,?,?,?,?,?,?)');
        $s->execute($data);
    }
} catch(Throwable $e) {
    foreach($createdFiles as $file){ if(is_file($file)) @unlink($file); }
    exit('Could not save movie. Please check that the title/slug is unique and try again.');
}

if($id){
    if($posterName!==null && str_starts_with($oldPoster,'/uploads/posters/')){
        $oldFile=realpath(__DIR__.'/..'.$oldPoster);
        $base=realpath(__DIR__.'/../uploads/posters');
        if($oldFile && $base && str_starts_with($oldFile,$base.DIRECTORY_SEPARATOR) && is_file($oldFile)) @unlink($oldFile);
    }
    if($videoName!==null && str_starts_with($oldVideo,'/uploads/movies/')){
        $oldFile=realpath(__DIR__.'/..'.$oldVideo);
        $base=realpath(__DIR__.'/../uploads/movies');
        if($oldFile && $base && str_starts_with($oldFile,$base.DIRECTORY_SEPARATOR) && is_file($oldFile)) @unlink($oldFile);
    }
}

header('Location: movies.php'); exit;
