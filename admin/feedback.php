<?php
session_start();
include("../config/database.php");

$page_title = "Feedback";
include("includes/header.php");

$feedbacks = $conn->query(
    "SELECT f.message, f.created_at, u.name
     FROM feedback f
     JOIN users u ON f.user_id = u.id
     ORDER BY f.id DESC"
)->fetch_all(MYSQLI_ASSOC);
?>

<h1 class="page-title">💬 Voter Feedback</h1>
<p class="page-subtitle">Read all feedback submitted by voters.</p>

<div class="card">
    <div class="actions-row">
        <div class="card-title" style="margin:0;border:none;padding:0;">
            <?php echo count($feedbacks); ?> feedback submissions
        </div>
        <div class="search-bar">
            <span class="search-icon">🔍</span>
            <input type="text" id="searchBox" placeholder="Search feedback…">
        </div>
    </div>

    <div class="table-wrap">
        <table class="data-table" id="feedbackTable">
            <thead>
                <tr><th>Voter</th><th>Message</th><th>Date</th></tr>
            </thead>
            <tbody>
            <?php if(empty($feedbacks)): ?>
                <tr><td colspan="3" style="text-align:center;color:#64748b;padding:24px;">No feedback submitted yet.</td></tr>
            <?php else: ?>
            <?php foreach($feedbacks as $row): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['message']); ?></td>
                    <td style="white-space:nowrap;color:#64748b;font-size:.82rem;">
                        <?php echo $row['created_at'] ? date('d M Y, H:i', strtotime($row['created_at'])) : '—'; ?>
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
    document.querySelectorAll("#feedbackTable tbody tr").forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(val) ? "" : "none";
    });
});
</script>

<?php include("includes/footer.php"); ?>
