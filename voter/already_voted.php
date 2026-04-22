<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$page_title = "Already Voted";
include("includes/header.php");
?>

<div class="status-page">
    <div class="already-voted-card">
        <div class="status-icon">🚫</div>
        <h1 style="color:#92400e;">Already Voted!</h1>
        <p>You have already cast your vote in this election. Each voter is only allowed to vote once to keep results fair.</p>
        <a href="dashboard.php" class="btn btn-primary">← Back to Dashboard</a>
    </div>
</div>

<?php include("includes/footer.php"); ?>
