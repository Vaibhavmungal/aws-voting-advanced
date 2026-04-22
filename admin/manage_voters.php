<?php
session_start();
include("../config/database.php");

// Auto-create password reset requests table if not present
$conn->query("CREATE TABLE IF NOT EXISTS `password_reset_requests` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `voter_name` VARCHAR(100) NOT NULL,
  `voter_email` VARCHAR(100) NOT NULL,
  `message` TEXT DEFAULT NULL,
  `status` ENUM('pending','resolved') DEFAULT 'pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

$page_title = "Manage Voters";
include("includes/header.php");

// ── Handle Delete ──────────────────────────────────────────────
$msg = '';
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    // Delete associated votes first (foreign key safety)
    $stmt = $conn->prepare("DELETE FROM votes WHERE user_id = ?");
    $stmt->bind_param('i', $del_id);
    $stmt->execute();
    $stmt->close();
    // Delete voter
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param('i', $del_id);
    $stmt->execute();
    $stmt->close();
    // Log action
    $conn->query("INSERT INTO logs (action) VALUES ('Admin deleted voter ID $del_id')");
    header("Location: manage_voters.php?msg=deleted");
    exit();
}
if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
}

// ── Fetch voters ───────────────────────────────────────────────
// Safe whitelist filter — no user input goes into SQL
$filter = $_GET['filter'] ?? 'all';
$where  = match($filter) {
    'voted'   => "WHERE has_voted=1",
    'unvoted' => "WHERE has_voted=0",
    default   => "",
};

$voters      = $conn->query("SELECT * FROM users $where ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
$total       = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
$voted_count = $conn->query("SELECT COUNT(*) AS c FROM users WHERE has_voted=1")->fetch_assoc()['c'];

// Pending password reset requests count
$reset_pending = $conn->query("SELECT COUNT(*) AS c FROM password_reset_requests WHERE status='pending' LIMIT 1")->fetch_assoc()['c'] ?? 0;
?>

<h1 class="page-title">🧑‍🎓 Voters List</h1>
<p class="page-subtitle">Browse, filter and manage all registered voters.</p>

<?php if ($msg === 'deleted'): ?>
    <div class="alert alert-danger">🗑️ Voter deleted successfully (and their votes removed).</div>
<?php endif; ?>

<?php if ($reset_pending > 0): ?>
    <div class="alert alert-warning">
        🔑 <strong><?php echo $reset_pending; ?> voter(s)</strong> have submitted a password reset request.
        <a href="reset_requests.php" class="btn btn-warning btn-sm" style="margin-left:12px;">View Requests</a>
    </div>
<?php endif; ?>

<div class="stats-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-num"><?php echo $total; ?></div>
        <div class="stat-label">Total Registered</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-num"><?php echo $voted_count; ?></div>
        <div class="stat-label">Voted</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">⏳</div>
        <div class="stat-num"><?php echo $total - $voted_count; ?></div>
        <div class="stat-label">Not Yet Voted</div>
    </div>
</div>

<div class="card">
    <div class="actions-row">
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="?filter=all"     class="btn btn-sm <?php echo $filter==='all'     ? 'btn-primary' : 'btn-outline'; ?>">All</a>
            <a href="?filter=voted"   class="btn btn-sm <?php echo $filter==='voted'   ? 'btn-success' : 'btn-outline'; ?>">✅ Voted</a>
            <a href="?filter=unvoted" class="btn btn-sm <?php echo $filter==='unvoted' ? 'btn-warning' : 'btn-outline'; ?>">⏳ Not Voted</a>
            <a href="add_voter.php"   class="btn btn-primary btn-sm">➕ Add Voter</a>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            <div class="search-bar">
                <span class="search-icon">🔍</span>
                <input type="text" id="searchBox" placeholder="Search voters…">
            </div>
            <a href="export.php" class="btn btn-outline btn-sm">📥 Export Excel</a>
            <a href="reset_requests.php" class="btn btn-outline btn-sm">🔑 Reset Requests
                <?php if ($reset_pending > 0): ?>
                    <span style="background:#ef4444;color:#fff;border-radius:50px;padding:1px 7px;font-size:.7rem;margin-left:4px;"><?php echo $reset_pending; ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    <div class="table-wrap">
        <table class="data-table" id="voterTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($voters)): ?>
                <tr><td colspan="6" style="text-align:center;color:#6b7280;padding:24px;">No voters found.</td></tr>
            <?php else: ?>
            <?php foreach ($voters as $row): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['type'] ?? '—'); ?></td>
                    <td>
                        <?php if ($row['has_voted']): ?>
                            <span class="badge badge-success">✅ Voted</span>
                        <?php else: ?>
                            <span class="badge badge-warning">⏳ Pending</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?delete=<?php echo $row['id']; ?>&filter=<?php echo urlencode($filter); ?>"
                           class="btn btn-danger btn-xs"
                           onclick="return confirm('⚠️ Delete voter \"<?php echo addslashes(htmlspecialchars($row['name'])); ?>\"?\n\nThis will also remove all their votes. This cannot be undone.')">
                            🗑️ Delete
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById("searchBox").addEventListener("keyup", function(){
    const val = this.value.toLowerCase();
    document.querySelectorAll("#voterTable tbody tr").forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(val) ? "" : "none";
    });
});
</script>

<?php include("includes/footer.php"); ?>