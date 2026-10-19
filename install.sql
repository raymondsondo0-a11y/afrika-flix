-- InfinityFree: tengeneza database kupitia Control Panel kwanza, kisha chagua database hiyo phpMyAdmin na u-import faili hili.
CREATE TABLE IF NOT EXISTS campaigns (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS settings (
 id TINYINT UNSIGNED PRIMARY KEY DEFAULT 1,
 monthly_budget INT UNSIGNED NOT NULL DEFAULT 500000,
 business_name VARCHAR(255) NOT NULL DEFAULT 'Biashara Yangu - Mbeya',
 phone VARCHAR(20) NULL,
 currency VARCHAR(10) NOT NULL DEFAULT 'TZS'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT IGNORE INTO settings (id,monthly_budget,business_name,currency) VALUES (1,500000,'Biashara Yangu - Mbeya','TZS');
INSERT INTO campaigns (campaign_name,platform,post_date,post_time,cost,type,status,notes)
SELECT 'Tangazo la Asubuhi','Facebook',CURDATE(),'08:00:00',10000,'Paid Boost','Scheduled','Mfano wa tangazo' WHERE NOT EXISTS (SELECT 1 FROM campaigns);
INSERT INTO campaigns (campaign_name,platform,post_date,post_time,cost,type,status,notes)
SELECT 'WhatsApp Status','WhatsApp',CURDATE(),'19:00:00',0,'Organic','Scheduled','Mfano wa status' WHERE NOT EXISTS (SELECT 1 FROM campaigns WHERE campaign_name='WhatsApp Status');
