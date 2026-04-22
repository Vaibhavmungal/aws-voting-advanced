<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

if(!isset($_GET['election'])){
    header("Location: dashboard.php");
    exit();
}

$uid         = (int)$_SESSION['user'];
$election_id = (int)$_GET['election'];

// Get election name (prepared statement)
$stmt = $conn->prepare("SELECT title FROM elections WHERE id = ?");
$stmt->bind_param("i", $election_id);
$stmt->execute();
$election = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get last voted candidate (prepared statement)
$stmt = $conn->prepare(
    "SELECT c.name
     FROM votes v
     JOIN candidates c ON v.candidate_id = c.id
     WHERE v.user_id = ? AND v.election_id = ?
     ORDER BY v.id DESC
     LIMIT 1"
);
$stmt->bind_param("ii", $uid, $election_id);
$stmt->execute();
$candidate = $stmt->get_result()->fetch_assoc();
$stmt->close();

$page_title = "Vote Submitted";
include("includes/header.php");
?>

<div class="status-page">
    <div class="success-card">
        <div class="status-icon">🎉</div>
        <h1 style="color:#059669;">Vote Submitted!</h1>
        <p>Your vote has been securely recorded. Thank you for participating in the democratic process.</p>

        <div class="success-detail">
            <p>Election</p>
            <strong><?php echo htmlspecialchars($election['title'] ?? 'N/A'); ?></strong>
        </div>

        <div class="success-detail">
            <p>You voted for</p>
            <strong><?php echo htmlspecialchars($candidate['name'] ?? 'Not Found'); ?></strong>
        </div>

        <a href="dashboard.php" class="btn btn-success" style="width:100%;text-align:center;margin-top:10px;">
            ← Back to Dashboard
        </a>
    </div>
</div>

<?php include("includes/footer.php"); ?>
