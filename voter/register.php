<?php
session_start();
include("../config/database.php");

// Already logged in → redirect
if(isset($_SESSION['user'])){
    header("Location: dashboard.php");
    exit();
}

$error   = '';
$success = '';

if(isset($_POST['register'])){
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password =      $_POST['password'] ?? '';

    $allowed_domain = "@college.edu"; // change to your real domain

    if(empty($name) || empty($email) || empty($password)){
        $error = "All fields are required.";
    } elseif(substr($email, -strlen($allowed_domain)) !== $allowed_domain){
        $error = "Only official college emails (@college.edu) are allowed.";
    } elseif(strlen($password) < 6){
        $error = "Password must be at least 6 characters.";
    } else {
        // Check duplicate email (prepared statement)
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows > 0){
            $error = "This email is already registered.";
            $stmt->close();
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt->close();

            // Insert new user (prepared statement)
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, type) VALUES (?, ?, ?, 'College')");
            $stmt->bind_param("sss", $name, $email, $hashed);
            $stmt->execute();
            $stmt->close();

            $success = "Account created successfully! You can now login.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="VoteSecure – Voter Registration">
    <title>Voter Registration – VoteSecure</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/voter.css">
    <style>
        body { background: linear-gradient(135deg,#1e1b4b 0%,#312e81 50%,#4338ca 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 32px 16px; }
        .auth-card { background: #fff; border-radius: 20px; padding: 48px 40px; width: 100%; max-width: 440px; box-shadow: 0 25px 60px rgba(0,0,0,.35); }
        .auth-logo { text-align: center; margin-bottom: 28px; }
        .auth-logo .logo-icon { font-size: 3rem; display: block; }
        .auth-logo h1 { font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-top: 6px; }
        .auth-logo p { font-size: .85rem; color: #64748b; margin-top: 4px; }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="auth-logo">
        <span class="logo-icon">📝</span>
        <h1>Create Account</h1>
        <p>Register to participate in elections</p>
    </div>

    <?php if($error):   ?><div class="alert alert-danger">⚠️ <?php echo htmlspecialchars($error);   ?></div><?php endif; ?>
    <?php if($success): ?><div class="alert alert-success">✅ <?php echo htmlspecialchars($success); ?></div><?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" placeholder="John Doe" required
                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="email">College Email</label>
            <input type="email" id="email" name="email" placeholder="you@college.edu" required
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="password">Password <small style="color:#94a3b8;">(min. 6 characters)</small></label>
            <input type="password" id="password" name="password" placeholder="Create a strong password" required>
        </div>
        <button type="submit" name="register" class="btn btn-primary" style="width:100%;margin-top:6px;">
            Create Account →
        </button>
    </form>

    <p class="text-center mt-4" style="font-size:.9rem;color:#64748b;">
        Already have an account? <a href="login.php">Sign In</a>
    </p>
</div>

</body>
</html>
