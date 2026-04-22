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
        if (str_starts_with(trim($line), '#')) continue; // skip comments
        if (str_contains($line, '=')) {
            [$key, $val] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($val);
        }
    }
}

// --- Connection ------------------------------------------------------
$db_host = $_ENV['DB_HOST'] ?? 'localhost';
$db_user = $_ENV['DB_USER'] ?? 'root';
$db_pass = $_ENV['DB_PASS'] ?? '';
$db_name = $_ENV['DB_NAME'] ?? 'aws_voting';

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
    die("❌ Database Connection Failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');
?>