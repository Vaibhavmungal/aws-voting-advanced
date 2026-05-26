<?php
/**
 * Database Configuration
 * Reads credentials from the .env file in the project root.
 * Fallback to hard-coded defaults if .env is missing.
 */

// --- Load .env -------------------------------------------------------
$env_file = dirname(__DIR__) . '/.env';
if (file_exists($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $trimmed_line = trim($line);
        if (str_starts_with($trimmed_line, '#')) continue; // skip comments
        if (str_contains($line, '=')) {
            [$key, $val] = explode('=', $line, 2);
            $val = trim($val);
            // Strip inline comments starting with #
            if (str_contains($val, '#')) {
                $val = explode('#', $val, 2)[0];
            }
            $_ENV[trim($key)] = trim($val);
        }
    }
}

// --- Connection ------------------------------------------------------
$db_host = $_ENV['DB_HOST'] ?? 'localhost';
$db_user = $_ENV['DB_USER'] ?? 'root';
$db_pass = $_ENV['DB_PASS'] ?? '';
$db_name = $_ENV['DB_NAME'] ?? 'aws_voting';

try {
    // Disable strict error exceptions temporarily or catch them cleanly
    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    if (!$conn) {
        die("❌ Database Connection Failed: " . mysqli_connect_error());
    }
    mysqli_set_charset($conn, 'utf8mb4');

    // --- Auto Migration: Ensure candidates table has manifesto column ---
    $table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'candidates'");
    if ($table_exists && mysqli_num_rows($table_exists) > 0) {
        $check_col = mysqli_query($conn, "SHOW COLUMNS FROM `candidates` LIKE 'manifesto'");
        if ($check_col && mysqli_num_rows($check_col) == 0) {
            mysqli_query($conn, "ALTER TABLE `candidates` ADD COLUMN `manifesto` TEXT DEFAULT NULL");
        }
    }
} catch (Throwable $e) {
    die("❌ Database Connection/Query Failed: " . $e->getMessage());
}
?>