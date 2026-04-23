<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Log the export action
$conn->query("INSERT INTO logs (action) VALUES ('Admin exported voters list to Excel')");

// Fetch all voters
$voters = $conn->query("
    SELECT id, name, email, type,
           IF(has_voted=1,'Voted','Pending') AS status,
           created_at
    FROM users
    ORDER BY id DESC
")->fetch_all(MYSQLI_ASSOC);

// Set headers to force Excel download
$filename = 'voters_export_' . date('Y-m-d_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

// BOM for Excel UTF-8 compatibility
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Header row
fputcsv($output, ['ID', 'Full Name', 'Email', 'Type', 'Status', 'Registered At']);

// Data rows
foreach ($voters as $row) {
    fputcsv($output, [
        $row['id'],
        $row['name'],
        $row['email'],
        $row['type'] ?? '—',
        $row['status'],
        $row['created_at'] ?? '—',
    ]);
}

fclose($output);
exit();
