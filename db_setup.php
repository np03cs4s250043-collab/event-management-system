<?php
//database table setsup

$host   = 'localhost';
$user   = 'root';
$pass   = '';         
$dbName = 'ems_db';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    //if already exixts then drop the database
    $pdo->exec("DROP DATABASE IF EXISTS `$dbName`");
    
    $pdo->exec("CREATE DATABASE `$dbName` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbName`");
    echo "<pre style='font-family:monospace;font-size:13px;padding:20px;'>🚀 Setting up EMS Database...\n\n";

    //users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `name`            VARCHAR(120)  NOT NULL,
        `email`           VARCHAR(180)  NOT NULL UNIQUE,
        `phone`           VARCHAR(20)   DEFAULT NULL,
        `password_hash`   VARCHAR(255)  NOT NULL,
        `role`            ENUM('admin','organizer','attendee','vendor') NOT NULL DEFAULT 'attendee',
        `profile_picture` VARCHAR(255)  DEFAULT NULL,
        `is_verified`     TINYINT(1)    NOT NULL DEFAULT 0,
        `is_active`       TINYINT(1)    NOT NULL DEFAULT 1,
        `created_at`      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at`      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_email` (`email`),
        INDEX `idx_role`  (`role`)
    ) ENGINE=InnoDB");
    echo "Table 'users' is ready.\n";

    //cactegories of event
    $pdo->exec("CREATE TABLE IF NOT EXISTS `event_categories` (
        `id`         TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `name`       VARCHAR(80) NOT NULL UNIQUE,
        `slug`       VARCHAR(80) NOT NULL UNIQUE,
        `created_at` TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    $pdo->exec("INSERT IGNORE INTO `event_categories` (`name`, `slug`) VALUES
        ('Concert','concert'),('Conference','conference'),('Workshop','workshop'),
        ('Webinar','webinar'),('Sports','sports'),('Festival','festival'),
        ('Exhibition','exhibition'),('Networking','networking'),
        ('Music Events','music-events'),('Football','football'),('Cricket','cricket')");
    echo "Table 'event_categories'  is ready and seeded.\n";

    //events table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `events` (
        `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `organizer_id` INT UNSIGNED NOT NULL,
        `category_id`  TINYINT UNSIGNED NOT NULL,
        `title`        VARCHAR(200) NOT NULL,
        `slug`         VARCHAR(220) NOT NULL UNIQUE,
        `description`  TEXT         NOT NULL,
        `venue`        VARCHAR(255) NOT NULL,
        `city`         VARCHAR(100) NOT NULL,
        `date_start`   DATETIME     NOT NULL,
        `date_end`     DATETIME     NOT NULL,
        `capacity`     SMALLINT UNSIGNED NOT NULL DEFAULT 100,
        `cover_image`  VARCHAR(255) DEFAULT NULL,
        `is_recurring` TINYINT(1)   NOT NULL DEFAULT 0,
        `recurrence`   ENUM('none','daily','weekly','monthly') NOT NULL DEFAULT 'none',
        `status`       ENUM('draft','published','cancelled','completed') NOT NULL DEFAULT 'draft',
        `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT `fk_events_organizer` FOREIGN KEY (`organizer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `fk_events_category`  FOREIGN KEY (`category_id`)  REFERENCES `event_categories`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
        INDEX `idx_status`     (`status`),
        INDEX `idx_date_start` (`date_start`),
        INDEX `idx_city`       (`city`),
        FULLTEXT INDEX `ft_search` (`title`, `description`, `venue`, `city`)
    ) ENGINE=InnoDB");
    echo "Table 'events' is ready.\n";

    //ticket issuing table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `tickets` (
        `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `event_id`      INT UNSIGNED NOT NULL,
        `name`          VARCHAR(80)  NOT NULL,
        `price`         DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        `quantity`      SMALLINT UNSIGNED NOT NULL DEFAULT 50,
        `quantity_sold` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        `sale_start`    DATETIME DEFAULT NULL,
        `sale_end`      DATETIME DEFAULT NULL,
        `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT `fk_tickets_event` FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        INDEX `idx_event_id` (`event_id`)
    ) ENGINE=InnoDB");
    echo "Table 'tickets' is ready.\n";

    //bookings table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `bookings` (
        `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `booking_ref`  VARCHAR(20)   NOT NULL UNIQUE,
        `attendee_id`  INT UNSIGNED  NOT NULL,
        `event_id`     INT UNSIGNED  NOT NULL,
        `ticket_id`    INT UNSIGNED  NOT NULL,
        `quantity`     TINYINT UNSIGNED NOT NULL DEFAULT 1,
        `total_amount` DECIMAL(10,2) NOT NULL,
        `status`       ENUM('pending','confirmed','cancelled','attended') NOT NULL DEFAULT 'pending',
        `qr_code`      VARCHAR(255)  DEFAULT NULL,
        `cancelled_at` TIMESTAMP     NULL DEFAULT NULL,
        `created_at`   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at`   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT `fk_bookings_attendee` FOREIGN KEY (`attendee_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
        CONSTRAINT `fk_bookings_event`    FOREIGN KEY (`event_id`)    REFERENCES `events`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
        CONSTRAINT `fk_bookings_ticket`   FOREIGN KEY (`ticket_id`)   REFERENCES `tickets`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
        INDEX `idx_attendee_id` (`attendee_id`),
        INDEX `idx_event_id`    (`event_id`),
        INDEX `idx_status`      (`status`)
    ) ENGINE=InnoDB");
    echo "Table 'bookings' is ready.\n";

    //payments table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `payments` (
        `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `booking_id`     INT UNSIGNED NOT NULL,
        `amount`         DECIMAL(10,2) NOT NULL,
        `currency`       VARCHAR(5)   NOT NULL DEFAULT 'NPR',
        `method`         ENUM('card','khalti','esewa','paypal','bank_transfer','promo_code') NOT NULL,
        `gateway_txn_id` VARCHAR(255) DEFAULT NULL,
        `status`         ENUM('pending','success','failed','refunded') NOT NULL DEFAULT 'pending',
        `paid_at`        TIMESTAMP    NULL DEFAULT NULL,
        `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT `fk_payments_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
        INDEX `idx_booking_id` (`booking_id`),
        INDEX `idx_status`     (`status`)
    ) ENGINE=InnoDB");
    echo "Table 'payments' is ready.\n";

    //waiting list
    $pdo->exec("CREATE TABLE IF NOT EXISTS `waitlist` (
        `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `event_id`   INT UNSIGNED NOT NULL,
        `user_id`    INT UNSIGNED NOT NULL,
        `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY `uq_waitlist` (`event_id`, `user_id`),
        CONSTRAINT `fk_waitlist_event` FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `fk_waitlist_user`  FOREIGN KEY (`user_id`)  REFERENCES `users`(`id`)  ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB");
    echo "Table 'waitlist' is ready.\n";

    //reviews table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `reviews` (
        `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `event_id`   INT UNSIGNED NOT NULL,
        `user_id`    INT UNSIGNED NOT NULL,
        `rating`     TINYINT UNSIGNED NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
        `comment`    TEXT         DEFAULT NULL,
        `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY `uq_review` (`event_id`, `user_id`),
        CONSTRAINT `fk_reviews_event` FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `fk_reviews_user`  FOREIGN KEY (`user_id`)  REFERENCES `users`(`id`)  ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB");
    echo "Table 'reviews' is ready.\n";

    //resetting password
    $pdo->exec("CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
        `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `user_id`    INT UNSIGNED NOT NULL,
        `token_hash` VARCHAR(255) NOT NULL,
        `expires_at` DATETIME     NOT NULL,
        `is_used`    TINYINT(1)   NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT `fk_prt_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        INDEX `idx_token_hash` (`token_hash`)
    ) ENGINE=InnoDB");
    echo "Table 'password_reset_tokens' is ready.\n";

    //table for audit logs
    $pdo->exec("CREATE TABLE IF NOT EXISTS `audit_logs` (
        `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `user_id`     INT UNSIGNED  DEFAULT NULL,
        `action`      VARCHAR(100)  NOT NULL,
        `entity_type` VARCHAR(50)   DEFAULT NULL,
        `entity_id`   INT UNSIGNED  DEFAULT NULL,
        `ip_address`  VARCHAR(45)   DEFAULT NULL,
        `user_agent`  VARCHAR(255)  DEFAULT NULL,
        `meta`        JSON          DEFAULT NULL,
        `created_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_user_id` (`user_id`),
        INDEX `idx_action`  (`action`),
        INDEX `idx_created` (`created_at`)
    ) ENGINE=InnoDB");
    echo "Table 'audit_logs'is  ready.\n";

    //Default Admin User
    $adminPassword = password_hash('admin', PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt = $pdo->prepare("INSERT IGNORE INTO `users` (`name`,`email`,`password_hash`,`role`,`is_verified`,`is_active`) VALUES (:name,:email,:pw,'admin',1,1)");
    $stmt->execute([':name' => 'System Admin', ':email' => 'admin', ':pw' => $adminPassword]);
    echo "\nDefault admin seeded.\n";

    echo "\n";
    echo "Database Setup Completed Successfully!!\n" ;
    echo "Default admin username: admin\n" ;
    echo "Default admin password: admin\n" ;
    echo "</pre>";

} catch (PDOException $e) {
    die("<pre style='color:red;'>Database Setup failed: " . htmlspecialchars($e->getMessage()) . "</pre>");
}
