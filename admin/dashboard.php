<?php
session_start();
include("../config/database.php");

$page_title = "Dashboard";
include("includes/header.php");

// Stats (all use simple COUNT queries — no user input, safe as-is)
$total_elections  = $conn->query("SELECT COUNT(*) AS c FROM elections")->fetch_assoc()['c'];
$active_elections = $conn->query("SELECT COUNT(*) AS c FROM elections WHERE status='Active'")->fetch_assoc()['c'];
$total_voters     = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
$voted_count      = $conn->query("SELECT COUNT(*) AS c FROM users WHERE has_voted=1")->fetch_assoc()['c'];
$total_candidates = $conn->query("SELECT COUNT(*) AS c FROM candidates")->fetch_assoc()['c'];
$total_votes      = $conn->query("SELECT COUNT(*) AS c FROM votes")->fetch_assoc()['c'];

// Recent elections
$recent = $conn->query("SELECT * FROM elections ORDER BY id DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>

<h1 class="page-title">Welcome back, <?php echo htmlspecialchars($_SESSION['admin']); ?> 👋</h1>
<p class="page-subtitle">Here's what's happening in your voting system today.</p>

<!-- STATS -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">🗳️</div>
        <div class="stat-num"><?php echo $total_elections; ?></div>
        <div class="stat-label">Total Elections</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🟢</div>
        <div class="stat-num"><?php echo $active_elections; ?></div>
        <div class="stat-label">Active Elections</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🧑‍🎓</div>
        <div class="stat-num"><?php echo $total_voters; ?></div>
        <div class="stat-label">Registered Voters</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-num"><?php echo $voted_count; ?></div>
        <div class="stat-label">Votes Cast</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">👤</div>
        <div class="stat-num"><?php echo $total_candidates; ?></div>
        <div class="stat-label">Candidates</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📊</div>
        <div class="stat-num"><?php echo $total_votes; ?></div>
        <div class="stat-label">Total Votes</div>
    </div>
</div>

<!-- QUICK ACTIONS -->
<div class="card" style="margin-bottom:24px;">
    <div class="card-title">⚡ Quick Actions</div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="manage_elections.php"  class="btn btn-primary">🗳️ Manage Elections</a>
        <a href="manage_candidates.php" class="btn btn-success">👤 Manage Candidates</a>
        <a href="manage_voters.php"     class="btn btn-outline">🧑‍🎓 View Voters</a>
        <a href="results.php"           class="btn btn-warning">🏆 View Results</a>
        <a href="feedback.php"          class="btn btn-outline">💬 Feedback</a>
    </div>
</div>

<!-- RECENT ELECTIONS -->
<div class="card">
    <div class="card-title">🗓️ Recent Elections</div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($recent)): ?>
                <tr><td colspan="5" style="text-align:center;color:#64748b;padding:24px;">No elections yet.</td></tr>
                <?php else: ?>
                <?php foreach($recent as $row): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['type']); ?></td>
                    <td><?php echo date('d M Y', strtotime($row['end_date'])); ?></td>
                    <td>
                        <span class="badge <?php echo $row['status']==='Active' ? 'badge-success' : 'badge-danger'; ?>">
                            <?php echo $row['status']; ?>
                        </span>
                    </td>
                    <td>
                        <a href="manage_elections.php?edit=<?php echo $row['id']; ?>" class="btn btn-outline btn-xs">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("includes/footer.php"); ?>