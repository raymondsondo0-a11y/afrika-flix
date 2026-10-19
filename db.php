<?php
declare(strict_types=1);
/* No credentials are stored in this public file. Create private/config.php on the server. */
$configFile=__DIR__.'/private/config.php';
if (!is_file($configFile)) {
  http_response_code(500);
  exit('Database haijawekwa bado. Tengeneza private/config.php kwenye hosting kwa kutumia db.example.php kama mwongozo.');
}
$config=require $configFile;
$host=$config['host']??'localhost'; $dbname=$config['dbname']??''; $username=$config['username']??''; $password=$config['password']??'';
$charset=$config['charset']??'utf8mb4';
try {
 $pdo=new PDO("mysql:host={$host};dbname={$dbname};charset={$charset}",$username,$password,[
  PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES=>false
 ]);
} catch (PDOException $ex) { error_log($ex->getMessage()); http_response_code(500); exit('Imeshindikana kuunganisha database. Kagua mipangilio ya database.'); }
