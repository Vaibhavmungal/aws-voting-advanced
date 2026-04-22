<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$uid     = (int)$_SESSION['user'];
$success = '';
$error   = '';

if(isset($_POST['submit_feedback'])){
    $message = trim($_POST['message'] ?? '');
    if(empty($message)){
        $error = "Feedback message cannot be empty.";
    } else {
        // Prepared statement
        $stmt = $conn->prepare("INSERT INTO feedback (user_id, message) VALUES (?, ?)");
        $stmt->bind_param("is", $uid, $message);
        $stmt->execute();
        $stmt->close();
        $success = "Thank you! Your feedback has been submitted.";
    }
}

$page_title = "Feedback";
include("includes/header.php");
?>

<h1 class="page-title">💬 Submit Feedback</h1>
<p class="page-subtitle">We value your opinion. Share your thoughts about the voting experience.</p>

<?php if($success): ?>
    <div class="alert alert-success">✅ <?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="alert alert-danger">⚠️ <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="card" style="max-width:600px;">
    <form method="POST">
        <div class="form-group">
            <label for="message">Your Feedback</label>
            <textarea id="message" name="message" rows="6" required
                      placeholder="Share your thoughts, suggestions, or concerns…"></textarea>
        </div>
        <button type="submit" name="submit_feedback" class="btn btn-primary">Send Feedback</button>
        <a href="dashboard.php" class="btn btn-outline" style="margin-left:10px;">Cancel</a>
    </form>
</div>

<?php include("includes/footer.php"); ?>