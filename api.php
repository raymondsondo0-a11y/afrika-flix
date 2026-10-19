<?php
declare(strict_types=1);session_start();require __DIR__.'/db.php';header('Content-Type: application/json; charset=utf-8');
if($_SERVER['REQUEST_METHOD']==='POST'){
 $data=json_decode(file_get_contents('php://input'),true)??[];if(!hash_equals($_SESSION['csrf']??'',(string)($data['csrf']??''))){http_response_code(403);echo json_encode(['error'=>'CSRF']);exit;}
 $id=filter_var($data['id']??null,FILTER_VALIDATE_INT);if(!$id){http_response_code(400);echo json_encode(['error'=>'ID si sahihi']);exit;}
 $q=$pdo->prepare("UPDATE campaigns SET status='Done' WHERE id=? AND status!='Cancelled'");$q->execute([$id]);echo json_encode(['ok'=>$q->rowCount()>0]);exit;
}
$platform=trim($_GET['platform']??'');$status=trim($_GET['status']??'');$from=trim($_GET['from']??'');$to=trim($_GET['to']??'');
$sql="SELECT id,campaign_name,platform,post_date,post_time,cost,type,status,notes FROM campaigns WHERE (?='' OR platform=?) AND (?='' OR status=?) AND (?='' OR post_date>=?) AND (?='' OR post_date<=?) ORDER BY post_date,post_time";
$q=$pdo->prepare($sql);$q->execute([$platform,$platform,$status,$status,$from,$from,$to,$to]);$colors=['Facebook'=>'#1877F2','Instagram'=>'#E4405F','TikTok'=>'#000000','WhatsApp'=>'#25D366','YouTube'=>'#FF0000'];$events=[];
foreach($q as $r){$events[]=['id'=>(string)$r['id'],'title'=>$r['campaign_name'].' · TZS '.number_format((int)$r['cost']),'start'=>$r['post_date'].'T'.$r['post_time'],'backgroundColor'=>$colors[$r['platform']]??'#64748b','borderColor'=>$colors[$r['platform']]??'#64748b','textColor'=>($r['platform']==='WhatsApp'?'#052e16':'#ffffff'),'url'=>'edit.php?id='.(int)$r['id'],'extendedProps'=>['status'=>$r['status'],'platform'=>$r['platform'],'notes'=>$r['notes']??'']];}
echo json_encode($events,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
