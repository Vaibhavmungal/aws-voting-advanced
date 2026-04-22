<?php
/**
 * Admin – Password Reset Requests
 * Shows all voter password reset requests with ability to mark as resolved.
 */
session_start();
include("../config/database.php");

// Auto-create table if missing
$conn->query("CREATE TABLE IF NOT EXISTS `password_reset_requests` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `voter_name` VARCHAR(100) NOT NULL,
  `voter_email` VARCHAR(100) NOT NULL,
  `message` TEXT DEFAULT NULL,
  `status` ENUM('pending','resolved') DEFAULT 'pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

$page_title = "Password Reset Requests";
include("includes/header.php");

// Handle mark-as-resolved
if (isset($_GET['resolve']) && is_numeric($_GET['resolve'])) {
    $rid  = (int)$_GET['resolve'];
    $stmt = $conn->prepare("UPDATE password_reset_requests SET status='resolved' WHERE id=?");
    $stmt->bind_param('i', $rid);
    $stmt->execute();
    $stmt->close();
    // Log the action
    $conn->query("INSERT INTO logs (action) VALUES ('Admin resolved password reset request #$rid')");
    header("Location: reset_requests.php?msg=resolved");
    exit();
}

// Handle delete request
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $rid  = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM password_reset_requests WHERE id=?");
    $stmt->bind_param('i', $rid);
    $stmt->execute();
    $stmt->close();
    header("Location: reset_requests.php?msg=deleted");
    exit();
}

// Fetch all requests
$filter = $_GET['status'] ?? 'all';
$where  = match($filter) {
    'pending'  => "WHERE status='pending'",
    'resolved' => "WHERE status='resolved'",
    default    => "",
};

$requests  = $conn->query("SELECT * FROM password_reset_requests $where ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
$pending   = $conn->query("SELECT COUNT(*) AS c FROM password_reset_requests WHERE status='pending'")->fetch_assoc()['c'];
$resolved  = $conn->query("SELECT COUNT(*) AS c FROM password_reset_requests WHERE status='resolved'")->fetch_assoc()['c'];

$msg = $_GET['msg'] ?? '';
?>

<h1 class="page-title">🔑 Password Reset Requests</h1>
<p class="page-subtitle">Voters who have forgotten their password and need your help.</p>

<?php if ($msg === 'resolved'): ?>
    <div class="alert alert-success">✅ Request marked as resolved.</div>
<?php elseif ($msg === 'deleted'): ?>
    <div class="alert alert-warning">🗑️ Request deleted.</div>
<?php endif; ?>

<!-- Stats -->
<div class="stats-grid" style="grid-template-columns:repeat(2,1fr);margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon">⏳</div>
        <div class="stat-num"><?php echo $pending; ?></div>
        <div class="stat-label">Pending Requests</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-num"><?php echo $resolved; ?></div>
        <div class="stat-label">Resolved</div>
    </div>
</div>

<div class="card">
    <!-- Filter tabs -->
    <div class="actions-row">
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="?status=all"      class="btn btn-sm <?php echo $filter==='all'      ? 'btn-primary':'btn-outline'; ?>">All</a>
            <a href="?status=pending"  class="btn btn-sm <?php echo $filter==='pending'  ? 'btn-warning':'btn-outline'; ?>">⏳ Pending</a>
            <a href="?status=resolved" class="btn btn-sm <?php echo $filter==='resolved' ? 'btn-success':'btn-outline'; ?>">✅ Resolved</a>
        </div>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Voter Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Requested At</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($requests)): ?>
                <tr><td colspan="7" style="text-align:center;color:#6b7280;padding:32px;">No requests found.</td></tr>
            <?php else: ?>
            <?php foreach ($requests as $r): ?>
                <tr>
                    <td><?php echo $r['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($r['voter_name']); ?></strong></td>
                    <td>
                        <a href="mailto:<?php echo htmlspecialchars($r['voter_email']); ?>">
                            <?php echo htmlspecialchars($r['voter_email']); ?>
                        </a>
                    </td>
                    <td style="max-width:220px;white-space:normal;font-size:.83rem;color:#6b7280;">
                        <?php echo !empty($r['message']) ? htmlspecialchars($r['message']) : '<em>—</em>'; ?>
                    </td>
                    <td style="font-size:.82rem;"><?php echo date('d M Y, H:i', strtotime($r['created_at'])); ?></td>
                    <td>
                        <?php if ($r['status'] === 'pending'): ?>
                            <span class="badge badge-warning">⏳ Pending</span>
                        <?php else: ?>
                            <span class="badge badge-success">✅ Resolved</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            <?php if ($r['status'] === 'pending'): ?>
                                <a href="?resolve=<?php echo $r['id']; ?>"
                                   class="btn btn-success btn-xs"
                                   onclick="return confirm('Mark this request as resolved?')">
                                    ✅ Resolve
                                </a>
                            <?php endif; ?>
                            <a href="?delete=<?php echo $r['id']; ?>"
                               class="btn btn-danger btn-xs"
                               onclick="return confirm('Delete this request permanently?')">
                                🗑️ Delete
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="alert alert-info" style="max-width:600px;">
    💡 <strong>How to reset a voter's password:</strong> Go to
    <a href="manage_voters.php">Manage Voters</a>, find the voter by email, then manually update
    their password via phpMyAdmin or ask them to re-register. Mark the request as Resolved once done.
</div>

<?php include("includes/footer.php"); ?>
