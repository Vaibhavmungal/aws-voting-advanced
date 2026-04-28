<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$page_title = "Vote";
$uid         = (int)$_SESSION['user'];

if(!isset($_GET['election'])){
    header("Location: dashboard.php");
    exit();
}

$election_id = (int)$_GET['election'];

// --- Check already voted (prepared statement) ---
$stmt = $conn->prepare("SELECT id FROM votes WHERE user_id = ? AND election_id = ?");
$stmt->bind_param("ii", $uid, $election_id);
$stmt->execute();
$stmt->store_result();
if($stmt->num_rows > 0){
    $stmt->close();
    header("Location: already_voted.php");
    exit();
}
$stmt->close();

// --- Get election info (prepared statement) ---
$stmt = $conn->prepare("SELECT title, end_date FROM elections WHERE id = ? AND status = 'Active'");
$stmt->bind_param("i", $election_id);
$stmt->execute();
$election = $stmt->get_result()->fetch_assoc();
$stmt->close();

if(!$election){
    header("Location: dashboard.php");
    exit();
}

// --- Handle vote submission ---
$error = '';
if(isset($_POST['vote'])){
    if(!isset($_POST['candidate'])){
        $error = "Please select a candidate before submitting.";
    } else {
        $candidate_id = (int)$_POST['candidate'];

        // --- Start Transaction for Safety ---
        $conn->begin_transaction();

        try {
            // Insert vote
            $stmt = $conn->prepare("INSERT INTO votes (user_id, candidate_id, election_id) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $uid, $candidate_id, $election_id);
            $stmt->execute();
            $stmt->close();

            // Update has_voted flag
            $stmt = $conn->prepare("UPDATE users SET has_voted = 1 WHERE id = ?");
            $stmt->bind_param("i", $uid);
            $stmt->execute();
            $stmt->close();

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Voting failed due to a system error. Please try again.";
        }

        if(empty($error)) {
            header("Location: vote_success.php?election=$election_id&candidate=$candidate_id");
            exit();
        }
    }
}

// --- Get candidates (prepared statement) ---
$stmt = $conn->prepare("SELECT id, name, position, image FROM candidates WHERE election_id = ?");
$stmt->bind_param("i", $election_id);
$stmt->execute();
$candidates = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

include("includes/header.php");
?>

<h1 class="page-title">🗳️ Cast Your Vote</h1>
<p class="page-subtitle">
    <?php echo htmlspecialchars($election['title']); ?> &nbsp;·&nbsp;
    <span style="color:#ef4444;">Ends: <?php echo date('d M Y', strtotime($election['end_date'])); ?></span>
</p>

<?php if($error): ?>
    <div class="alert alert-danger">⚠️ <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="card">
    <?php if(empty($candidates)): ?>
        <div class="empty-state">
            <div class="empty-icon">👤</div>
            <p>No candidates are available for this election yet.</p>
        </div>
    <?php else: ?>
        <form method="POST" id="voteForm">
            <div class="candidates-list" id="candidatesList">
                <?php foreach($candidates as $row): ?>
                <label class="candidate-item" for="candidate_<?php echo $row['id']; ?>">
                    <input type="radio" name="candidate"
                           id="candidate_<?php echo $row['id']; ?>"
                           value="<?php echo $row['id']; ?>">
                    <div class="candidate-inner">
                        <?php if(!empty($row['image'])): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        <?php else: ?>
                            <div style="width:56px;height:56px;border-radius:50%;background:var(--primary-light);display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0;">👤</div>
                        <?php endif; ?>
                        <div class="candidate-info">
                            <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                            <span><?php echo htmlspecialchars($row['position']); ?></span>
                        </div>
                        <div class="candidate-radio"></div>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>

            <hr class="divider">
            <button type="submit" name="vote" class="btn btn-primary">✅ Submit Vote</button>
        </form>
    <?php endif; ?>
</div>

<script>
// Highlight selected candidate
document.querySelectorAll('.candidate-item').forEach(function(item){
    item.addEventListener('click', function(){
        document.querySelectorAll('.candidate-item').forEach(el => el.classList.remove('selected'));
        this.classList.add('selected');
        this.querySelector('input[type="radio"]').checked = true;
    });
});
</script>

<?php include("includes/footer.php"); ?>
