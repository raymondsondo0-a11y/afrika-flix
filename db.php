<?php
declare(strict_types=1);
/*
 * Public database bootstrap. Real credentials belong only in private/config.php.
 * On first connection, required tables and sample rows are created automatically.
 */
$configFile=__DIR__.'/private/config.php';
if (!is_file($configFile)) {
 http_response_code(503);
 exit('Usanidi wa faragha unahitajika. Weka taarifa za MySQL kwenye private/config.php ndani ya htdocs, kisha refresh ukurasa huu.');
}
$config=require $configFile;
$host=$config['host']??'localhost'; $dbname=$config['dbname']??''; $username=$config['username']??''; $password=$config['password']??''; $charset=$config['charset']??'utf8mb4';
try {
 $pdo=new PDO("mysql:host={$host};dbname={$dbname};charset={$charset}",$username,$password,[
  PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES=>false
 ]);
 // Install schema automatically, so no manual SQL import is needed after adding private DB settings.
 $pdo->exec("CREATE TABLE IF NOT EXISTS campaigns (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  campaign_name VARCHAR(255) NOT NULL,
  platform ENUM('Facebook','Instagram','TikTok','WhatsApp','YouTube') NOT NULL,
  post_date DATE NOT NULL,
  post_time TIME NOT NULL,
  cost INT UNSIGNED NOT NULL DEFAULT 0,
  type ENUM('Organic','Paid Boost','Influencer','Poster') NOT NULL,
  status ENUM('Scheduled','Done','Cancelled') NOT NULL DEFAULT 'Scheduled',
  notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_post_date (post_date),
  INDEX idx_platform_date (platform,post_date),
  INDEX idx_status_date (status,post_date)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
 $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
  id TINYINT UNSIGNED PRIMARY KEY DEFAULT 1,
  monthly_budget INT UNSIGNED NOT NULL DEFAULT 500000,
  business_name VARCHAR(255) NOT NULL DEFAULT 'Biashara Yangu - Mbeya',
  phone VARCHAR(20) NULL,
  currency VARCHAR(10) NOT NULL DEFAULT 'TZS'
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
 $pdo->exec("INSERT IGNORE INTO settings (id,monthly_budget,business_name,currency) VALUES (1,500000,'Biashara Yangu - Mbeya','TZS')");
 $seed=$pdo->prepare("INSERT INTO campaigns(campaign_name,platform,post_date,post_time,cost,type,status,notes) SELECT ?,?,?,?, ?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM campaigns WHERE campaign_name=?)");
 $seed->execute(['Tangazo la Asubuhi','Facebook',date('Y-m-d'),'08:00:00',10000,'Paid Boost','Scheduled','Mfano wa tangazo','Tangazo la Asubuhi']);
 $seed->execute(['WhatsApp Status','WhatsApp',date('Y-m-d'),'19:00:00',0,'Organic','Scheduled','Mfano wa status','WhatsApp Status']);
} catch (PDOException $ex) {
 error_log('Ad Scheduler database error: '.$ex->getMessage());
 http_response_code(500);
 exit('Imeshindikana kuunganisha au kuanzisha database. Kagua taarifa za MySQL kwenye private/config.php.');
}
