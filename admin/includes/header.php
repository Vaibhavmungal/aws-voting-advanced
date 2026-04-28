<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
/**
 * Admin Shared Header
 * Expects: $page_title (string) set before include
 * Sets: $_SESSION['admin'] guard
 */
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}
$admin_name = htmlspecialchars($_SESSION['admin']);
$current    = basename($_SERVER['PHP_SELF']);

$nav_items = [
    'dashboard.php'         => ['icon' => '📊', 'label' => 'Dashboard'],
    'manage_elections.php'  => ['icon' => '🗳️',  'label' => 'Elections'],
    'manage_candidates.php' => ['icon' => '👤',  'label' => 'Candidates'],
    'manage_voters.php'     => ['icon' => '🧑‍🎓', 'label' => 'Voters'],
    'results.php'           => ['icon' => '🏆',  'label' => 'Results'],
    'feedback.php'          => ['icon' => '💬',  'label' => 'Feedback'],
    'reset_requests.php'    => ['icon' => '🔑',  'label' => 'Reset Requests'],
    'logs.php'              => ['icon' => '📝',  'label' => 'Audit Logs'],
    'about.php'             => ['icon' => 'ℹ️',  'label' => 'About'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="VoteSecure Admin – <?php echo htmlspecialchars($page_title ?? 'Panel'); ?>">
    <title><?php echo htmlspecialchars($page_title ?? 'Admin'); ?> – VoteSecure Admin</title>
    <!-- Preconnect to reduce Google Fonts latency -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Async font load: non-blocking render -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"></noscript>
    <link rel="stylesheet" href="../assets/css/admin.css">

</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">🗳️</div>
        <span class="brand-name">VoteSecure</span>
        <span class="brand-sub">Admin Panel</span>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Navigation</div>
        <?php foreach($nav_items as $file => $item): ?>
        <a href="<?php echo $file; ?>"
           class="sidebar-link <?php echo ($current === $file) ? 'active' : ''; ?>">
            <span class="nav-icon"><?php echo $item['icon']; ?></span>
            <span><?php echo $item['label']; ?></span>
        </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <a href="logout.php">
            <span>🚪</span>
            <span>Logout</span>
        </a>
    </div>
</aside>

<!-- MAIN WRAPPER -->
<div class="admin-wrapper">

    <!-- TOP BAR -->
    <div class="topbar">
        <span class="topbar-title"><?php echo htmlspecialchars($page_title ?? 'Dashboard'); ?></span>
        <div class="topbar-right">
            <span class="admin-badge">👤 <?php echo $admin_name; ?></span>
            <a href="logout.php" class="topbar-logout">🚪 Logout</a>
        </div>
    </div>

    <!-- PAGE CONTENT -->
    <div class="page-content">
