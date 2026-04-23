<?php
session_start();
include("../config/database.php");

$page_title = "Dashboard";
include("includes/header.php");

// ── Stats ────────────────────────────────────────────────────────────
$total_elections  = $conn->query("SELECT COUNT(*) AS c FROM elections")->fetch_assoc()['c'];
$active_elections = $conn->query("SELECT COUNT(*) AS c FROM elections WHERE status='Active'")->fetch_assoc()['c'];
$total_voters     = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
$voted_count      = $conn->query("SELECT COUNT(*) AS c FROM users WHERE has_voted=1")->fetch_assoc()['c'];
$total_candidates = $conn->query("SELECT COUNT(*) AS c FROM candidates")->fetch_assoc()['c'];
$total_votes      = $conn->query("SELECT COUNT(*) AS c FROM votes")->fetch_assoc()['c'];
$not_voted        = $total_voters - $voted_count;

// ── Recent elections (table) ─────────────────────────────────────────
$recent = $conn->query("SELECT * FROM elections ORDER BY id DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

// ── Chart data: votes per election (bar chart) ───────────────────────
$chart_elections = [];
$chart_votes     = [];
$res = $conn->query("
    SELECT e.title, COUNT(v.id) AS vote_count
    FROM elections e
    LEFT JOIN votes v ON v.election_id = e.id
    GROUP BY e.id
    ORDER BY e.id DESC
    LIMIT 8
");
while ($row = $res->fetch_assoc()) {
    $chart_elections[] = $row['title'];
    $chart_votes[]     = (int)$row['vote_count'];
}
$chart_elections_json = json_encode(array_reverse($chart_elections));
$chart_votes_json     = json_encode(array_reverse($chart_votes));
?>

<h1 class="page-title">Welcome back, <?php echo htmlspecialchars($_SESSION['admin']); ?> 👋</h1>
<p class="page-subtitle">Here's what's happening in your voting system today.</p>

<!-- ── STATS GRID ────────────────────────────────────────────────── -->
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
        <div class="stat-label">Voters Participated</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">👤</div>
        <div class="stat-num"><?php echo $total_candidates; ?></div>
        <div class="stat-label">Candidates</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📊</div>
        <div class="stat-num"><?php echo $total_votes; ?></div>
        <div class="stat-label">Total Vote Transactions</div>
    </div>
</div>

<!-- ── CHARTS ROW ─────────────────────────────────────────────────── -->
<div class="charts-row">

    <!-- Doughnut: Voter Participation -->
    <div class="card chart-card">
        <div class="card-title">🧩 Voter Participation</div>
        <div class="chart-wrap">
            <canvas id="participationChart"></canvas>
        </div>
        <div class="chart-legend">
            <span class="legend-dot" style="background:#6c63ff;"></span> Voted (<?php echo $voted_count; ?>)
            &nbsp;
            <span class="legend-dot" style="background:#e2e8f0;"></span> Not Yet (<?php echo $not_voted; ?>)
        </div>
    </div>

    <!-- Bar: Votes per Election -->
    <div class="card chart-card chart-card--wide">
        <div class="card-title">📊 Votes per Election</div>
        <div class="chart-wrap">
            <canvas id="votesBarChart"></canvas>
        </div>
    </div>

</div>

<!-- ── QUICK ACTIONS ──────────────────────────────────────────────── -->
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

<!-- ── RECENT ELECTIONS TABLE ─────────────────────────────────────── -->
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

<!-- ── CHART.JS ───────────────────────────────────────────────────── -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>
<script>
window.addEventListener('load', function() {
// ── Shared defaults ─────────────────────────────────────────────
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#94a3b8';

// ── Doughnut: Voter Participation ───────────────────────────────
const dCtx = document.getElementById('participationChart').getContext('2d');
new Chart(dCtx, {
    type: 'doughnut',
    data: {
        labels: ['Voted', 'Not Yet'],
        datasets: [{
            data: [<?php echo $voted_count; ?>, <?php echo max(0, $not_voted); ?>],
            backgroundColor: ['#6c63ff', '#1e293b'],
            borderColor: ['#8b84ff', '#334155'],
            borderWidth: 2,
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '72%',
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed} voters` } }
        },
        animation: { animateRotate: true, duration: 1000, easing: 'easeInOutQuart' }
    }
});

// ── Bar: Votes per Election ─────────────────────────────────────
const bCtx = document.getElementById('votesBarChart').getContext('2d');
const gradient = bCtx.createLinearGradient(0, 0, 0, 300);
gradient.addColorStop(0, 'rgba(108,99,255,0.9)');
gradient.addColorStop(1, 'rgba(108,99,255,0.15)');

new Chart(bCtx, {
    type: 'bar',
    data: {
        labels: <?php echo $chart_elections_json; ?>,
        datasets: [{
            label: 'Votes',
            data: <?php echo $chart_votes_json; ?>,
            backgroundColor: gradient,
            borderColor: '#6c63ff',
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.y} votes` } }
        },
        scales: {
            x: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { maxRotation: 30 } },
            y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { precision: 0 } }
        },
        animation: { duration: 1000, easing: 'easeInOutQuart' }
    }
});
}); // end window.load
</script>

<?php include("includes/footer.php"); ?>