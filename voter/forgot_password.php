<?php
/**
 * Voter – Forgot Password / Contact Admin Page
 * Creates table on first load if not present, then handles the request form.
 */
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
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

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['voter_name']  ?? '');
    $email   = trim($_POST['voter_email'] ?? '');
    $message = trim($_POST['message']     ?? '');

    if (empty($name) || empty($email)) {
        $error = 'Name and email are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Check if already pending
        $chk  = $conn->prepare("SELECT id FROM password_reset_requests WHERE voter_email = ? AND status = 'pending'");
        $chk->bind_param('s', $email);
        $chk->execute();
        $chk->store_result();

        if ($chk->num_rows > 0) {
            $error = 'A request for this email is already pending. The admin will reset it soon.';
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO password_reset_requests (voter_name, voter_email, message) VALUES (?, ?, ?)"
            );
            $stmt->bind_param('sss', $name, $email, $message);
            if ($stmt->execute()) {
                $success = 'Your request has been sent to the admin. They will reset your password shortly.';
            } else {
                $error = 'Something went wrong. Please try again.';
            }
            $stmt->close();
        }
        $chk->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="VoteSecure – Forgot Password">
    <title>Forgot Password – VoteSecure</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/voter.css">
    <script>
        // Force reload if loaded from Back-Forward Cache (bfcache)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
    <style>
        body {
            background:
                radial-gradient(ellipse 70% 60% at 50% -5%, rgba(124,58,237,.6), transparent),
                radial-gradient(ellipse 50% 50% at 80% 85%, rgba(245,158,11,.18), transparent),
                #0a0118;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding: 30px 16px;
        }
        body::before {
            content: '';
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.02) 1px, transparent 1px);
            background-size: 44px 44px;
            pointer-events: none;
        }
        .forgot-card {
            background: #fff;
            border-radius: 22px;
            padding: 50px 44px;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 30px 80px rgba(0,0,0,.5), 0 0 0 1px rgba(124,58,237,.12);
            position: relative;
            z-index: 1;
        }
        .card-header { text-align: center; margin-bottom: 32px; }
        .card-header .icon { font-size: 3.5rem; display: block; margin-bottom: 10px; }
        .card-header h1 { font-size: 1.5rem; font-weight: 800; color: #1e1b4b; }
        .card-header p  { font-size: .88rem; color: #6b7280; margin-top: 6px; line-height: 1.55; }

        .info-box {
            background: #ede9fe;
            border: 1px solid rgba(124,58,237,.3);
            border-radius: 10px;
            padding: 14px 16px;
            font-size: .85rem;
            color: #5b21b6;
            margin-bottom: 22px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
            line-height: 1.5;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: .88rem;
            color: #7c3aed;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="forgot-card">
    <div class="card-header">
        <span class="icon">🔑</span>
        <h1>Forgot Password?</h1>
        <p>No worries! Fill in your details and the admin will manually reset your password.</p>
    </div>

    <div class="info-box">
        ℹ️ Since this system doesn't use automatic email, your request will be visible to the admin who will contact you or reset your password directly.
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">✅ <?php echo htmlspecialchars($success); ?></div>
        <a href="login.php" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:10px;">← Back to Login</a>
    <?php else: ?>

        <?php if ($error): ?>
            <div class="alert alert-danger">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" id="forgotForm">
            <div class="form-group">
                <label for="voter_name">Your Full Name</label>
                <input type="text" id="voter_name" name="voter_name"
                       placeholder="Enter your registered name" required
                       value="<?php echo htmlspecialchars($_POST['voter_name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="voter_email">Registered Email</label>
                <input type="email" id="voter_email" name="voter_email"
                       placeholder="you@college.edu" required
                       value="<?php echo htmlspecialchars($_POST['voter_email'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="message">Additional Message <span style="color:#9ca3af;font-weight:400;">(optional)</span></label>
                <textarea id="message" name="message" placeholder="Any extra info for the admin…"><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                📨 Send Request to Admin
            </button>
        </form>

        <a href="login.php" class="back-link">← Back to Login</a>
    <?php endif; ?>
</div>

</body>
</html>
