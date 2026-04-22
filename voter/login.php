<?php
session_start();
include("../config/database.php");

// Already logged in → redirect
if(isset($_SESSION['user'])){
    header("Location: dashboard.php");
    exit();
}

$login_error = '';

if(isset($_POST['login'])){
    $email = trim($_POST['email'] ?? '');

    // Fetch user and verify password (prepared statement)
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if($user && password_verify($_POST['password'], $user['password'])){
        $_SESSION['user'] = $user['id'];
        header("Location: dashboard.php");
        exit();
    } else {
        $login_error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="VoteSecure – Voter Login">
    <title>Voter Login – VoteSecure</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/voter.css">
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
        .auth-card {
            background: #fff;
            border-radius: 22px;
            padding: 50px 42px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 30px 80px rgba(0,0,0,.5), 0 0 0 1px rgba(124,58,237,.12);
            position: relative;
            z-index: 1;
        }
        .auth-logo { text-align: center; margin-bottom: 30px; }
        .auth-logo .logo-icon { font-size: 3rem; display: block; }
        .auth-logo h1 { font-size: 1.5rem; font-weight: 800; color: #1e1b4b; margin-top: 10px; }
        .auth-logo p { font-size: .85rem; color: #6b7280; margin-top: 4px; }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="auth-logo">
        <span class="logo-icon">🗳️</span>
        <h1>VoteSecure</h1>
        <p>Sign in to your voter account</p>
    </div>

    <?php if($login_error): ?>
        <div class="alert alert-danger">⚠️ <?php echo htmlspecialchars($login_error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="you@college.edu" required
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>
        <button type="submit" name="login" class="btn btn-primary" style="width:100%;margin-top:6px;">
            Sign In →
        </button>
    </form>

    <p class="text-center mt-4" style="font-size:.9rem;color:#64748b;">
        Don't have an account? <a href="register.php">Register Here</a>
    </p>
    <p class="text-center" style="font-size:.88rem;margin-top:10px;">
        <a href="forgot_password.php" style="color:#7c3aed;font-weight:600;">🔑 Forgot Password? Contact Admin</a>
    </p>
</div>

</body>
</html>
