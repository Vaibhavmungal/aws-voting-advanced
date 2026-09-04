<?php
/**
 * Health Check Endpoint
 * Used by Docker HEALTHCHECK, AWS ALB/ELB target groups, and Kubernetes probes.
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

$response = [
    'status'    => 'healthy',
    'app'       => 'VoteSecure',
    'timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
    'php'       => PHP_VERSION,
    'database'  => 'unknown'
];

// Read env if file exists
$env_file = __DIR__ . '/.env';
if (file_exists($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if (str_starts_with($trimmed, '#')) continue;
        if (str_contains($line, '=')) {
            [$k, $v] = explode('=', $line, 2);
            $v = trim($v);
            if (str_contains($v, '#')) {
                $v = explode('#', $v, 2)[0];
            }
            $_ENV[trim($k)] = trim($v);
        }
    }
}

$db_host = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'localhost');
$db_user = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'root');
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : ($_ENV['DB_PASS'] ?? '');
$db_name = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'aws_voting');
$db_port = (int)(getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? 3306));

// Check DB connectivity non-blockingly
$link = @mysqli_init();
if ($link) {
    @mysqli_options($link, MYSQLI_OPT_CONNECT_TIMEOUT, 3);
    if (@mysqli_real_connect($link, $db_host, $db_user, $db_pass, $db_name, $db_port)) {
        $response['database'] = 'connected';
        mysqli_close($link);
    } else {
        $response['database'] = 'disconnected: ' . mysqli_connect_error();
        // If DB is required, mark degraded. Still return HTTP 200 so web server container is considered healthy during boot
        $response['status']   = 'degraded';
    }
}

// HTTP 200 signals web container is alive; only return 503 if PHP engine is failing
http_response_code(200);
echo json_encode($response, JSON_PRETTY_PRINT);
exit;
