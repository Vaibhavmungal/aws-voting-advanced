<?php
/**
 * Auto-creates the password_reset_requests table if it doesn't exist.
 * Run this once: http://localhost/aws-voting-advanced/database/setup_reset_table.php
 */
include("../config/database.php");

$sql = "CREATE TABLE IF NOT EXISTS `password_reset_requests` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `voter_name` VARCHAR(100) NOT NULL,
  `voter_email` VARCHAR(100) NOT NULL,
  `message` TEXT DEFAULT NULL,
  `status` ENUM('pending','resolved') DEFAULT 'pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

if($conn->query($sql)){
    echo "<h2 style='font-family:sans-serif;color:green;padding:30px;'>✅ Table <code>password_reset_requests</code> created (or already exists). You can close this page.</h2>";
} else {
    echo "<h2 style='font-family:sans-serif;color:red;padding:30px;'>❌ Error: " . $conn->error . "</h2>";
}
?>
