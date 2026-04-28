<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include("../config/database.php");

// Redirect if already logged in
if(isset($_SESSION['admin'])){
    header("Location: dashboard.php");
    exit();
}

$error = '';

if(isset($_POST['login'])){
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if(empty($username) || empty($password)){
        $error = "Both fields are required.";
    } else {
        // Fetch admin by username (prepared statement)
        $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // Support both plain-text (legacy) and hashed passwords
        $valid = $admin && (
            password_verify($password, $admin['password']) ||
            $admin['password'] === $password  // legacy plain-text fallback
        );

        if($valid){
            $_SESSION['admin'] = $admin['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="VoteSecure Admin Login">
    <title>Admin Login – VoteSecure</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script>
        // Force reload if loaded from Back-Forward Cache (bfcache)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">
            <span class="logo-icon">🔐</span>
            <h1>Admin Portal</h1>
            <p>VoteSecure Administration</p>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Admin username" required
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter password" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:6px;">
                Sign In →
            </button>
        </form>
    </div>
</div>

</body>
</html>
