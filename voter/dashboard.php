<?php
session_start();
include("../config/database.php");

$page_title = "Dashboard";

// header.php handles the auth redirect and fetches user name
include("includes/header.php");

// Active elections (prepared statement)
$uid  = (int)$_SESSION['user'];
$stmt = $conn->prepare("SELECT * FROM elections WHERE status = 'Active' ORDER BY end_date ASC");
$stmt->execute();
$elections = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Which elections has this user already voted in?
$stmt = $conn->prepare("SELECT election_id FROM votes WHERE user_id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$vr = $stmt->get_result();
$voted_in = [];
while($r = $vr->fetch_assoc()) $voted_in[] = $r['election_id'];
$stmt->close();
?>

<h1 class="page-title">Available Elections</h1>
<p class="page-subtitle">Click "Vote Now" to cast your ballot. You can only vote once per election.</p>

<?php if(empty($elections)): ?>
    <div class="empty-state">
        <div class="empty-icon">📭</div>
        <p>No active elections at the moment. Check back later.</p>
    </div>
<?php else: ?>
    <div class="elections-grid">
        <?php foreach($elections as $row): ?>
        <div class="election-card">
            <div>
                <span class="badge">🟢 Active</span>
            </div>
            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            <div class="election-meta">
                <span>📋 Type: <?php echo htmlspecialchars($row['type']); ?></span>
                <span>⏳ Ends: <?php echo date('d M Y', strtotime($row['end_date'])); ?></span>
            </div>

            <?php if(in_array($row['id'], $voted_in)): ?>
                <span class="btn btn-sm" style="background:#e2e8f0;color:#64748b;cursor:default;">✅ Already Voted</span>
            <?php else: ?>
                <a href="vote.php?election=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                    🗳️ Vote Now
                </a>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include("includes/footer.php"); ?>