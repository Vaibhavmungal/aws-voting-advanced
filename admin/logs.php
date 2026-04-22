<?php
session_start();
include("../config/database.php");

$page_title = "Audit Logs";
include("includes/header.php");

// Fetch logs
$logs = $conn->query("SELECT * FROM logs ORDER BY id DESC LIMIT 100")->fetch_all(MYSQLI_ASSOC);
?>

<h1 class="page-title">📝 System Audit Logs</h1>
<p class="page-subtitle">Track recent administrative actions and system events.</p>

<div class="card">
    <div class="card-title">Recent Activity (Last 100 entries)</div>
    
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 80px;">Log ID</th>
                    <th>Action Description</th>
                    <th style="width: 180px;">Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($logs)): ?>
                    <tr><td colspan="3" style="text-align:center;color:#64748b;padding:24px;">No system logs available yet.</td></tr>
                <?php else: ?>
                    <?php foreach($logs as $log): ?>
                        <tr>
                            <td style="color:#64748b;">#<?php echo $log['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($log['action']); ?></strong></td>
                            <td style="color:#64748b; font-size: .85rem;">
                                <?php echo date('d M Y, H:i:s', strtotime($log['created_at'])); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("includes/footer.php"); ?>
