<?php
// Shared header for all voter pages
// Expects: $page_title (string), $conn (mysqli connection), $_SESSION['user'] set
if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

// Fetch logged-in user's name
$uid  = (int)$_SESSION['user'];
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$result = $stmt->get_result();
$me     = $result->fetch_assoc();
$stmt->close();
$voter_name = htmlspecialchars($me['name'] ?? 'Voter');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Online Voting System – <?php echo htmlspecialchars($page_title ?? 'Voter Panel'); ?>">
    <title><?php echo htmlspecialchars($page_title ?? 'Voter Panel'); ?> – VoteSecure</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/voter.css">
</head>
<body>

<!-- TOP HEADER -->
<header class="top-header">
    <div class="brand">
        <span class="brand-icon">🗳️</span>
        <span class="brand-name">VoteSecure</span>
    </div>
    <div class="header-right">
        <span class="welcome-text">👤 <?php echo $voter_name; ?></span>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</header>

<!-- NAVBAR -->
<nav class="navbar">
    <a href="dashboard.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='dashboard.php')?'active':''; ?>">🏠 Home</a>
    <a href="profile.php"   class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='profile.php') ?'active':''; ?>">👤 Profile</a>
    <a href="feedback.php"  class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='feedback.php') ?'active':''; ?>">💬 Feedback</a>
</nav>

<!-- PAGE BODY STARTS BELOW -->
<main class="main-content">
