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
    $email    = trim($_POST['email']    ?? '');
    $password =      $_POST['password'] ?? '';

    if(empty($email) || empty($password)){
        $login_error = "Please enter your email and password.";
    } else {
        // Fetch user by email
        $stmt = $conn->prepare(
            "SELECT id, name, password FROM users WHERE email = ? LIMIT 1"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if($user){
            $stored = $user['password'];
            $valid  = false;

            // Hashed password (new users)
            if(password_verify($password, $stored)){
                $valid = true;
            }
            // Plain-text fallback (legacy users) + auto-upgrade
            elseif($password === $stored){
                $valid = true;
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $upd->bind_param("si", $newHash, $user['id']);
                $upd->execute();
                $upd->close();
            }

            if($valid){
                $_SESSION['user']      = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                header("Location: dashboard.php");
                exit();
            } else {
                $login_error = "Incorrect password. Please try again.";
            }
        } else {
            $login_error = "No account found with that email. Please register first.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="VoteSecure – Secure Voter Login">
    <title>Voter Login – VoteSecure</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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

        /* ── Card ── */
        .auth-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 52px 44px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 30px 80px rgba(0,0,0,.5), 0 0 0 1px rgba(124,58,237,.12);
            position: relative;
            z-index: 1;
            animation: slideUp .45s ease both;
        }
        @keyframes slideUp {
            from { opacity:0; transform:translateY(28px); }
            to   { opacity:1; transform:translateY(0); }
        }

        /* ── Logo ── */
        .auth-logo { text-align: center; margin-bottom: 32px; }
        .logo-icon-wrap {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 14px;
            box-shadow: 0 8px 28px rgba(124,58,237,.45);
        }
        .auth-logo h1 { font-size: 1.55rem; font-weight: 800; color: #1e1b4b; margin: 0; }
        .auth-logo p  { font-size: .85rem; color: #6b7280; margin-top: 5px; }

        /* ── Trusted badges ── */
        .trust-badges {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 26px;
            flex-wrap: wrap;
        }
        .trust-badge {
            background: #f5f3ff;
            border: 1px solid #ddd6fe;
            border-radius: 50px;
            padding: 4px 12px;
            font-size: .73rem;
            font-weight: 600;
            color: #6d28d9;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* ── Input icons ── */
        .input-icon-wrap {
            position: relative;
        }
        .input-icon-wrap .field-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1rem;
            pointer-events: none;
            opacity: .6;
        }
        .input-icon-wrap input {
            padding-left: 38px !important;
        }

        /* ── Show/hide password ── */
        .pass-wrap {
            position: relative;
        }
        .pass-wrap input { padding-right: 44px !important; }
        .toggle-pass {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: .85rem;
            color: #9ca3af;
            padding: 0;
            line-height: 1;
        }
        .toggle-pass:hover { color: #7c3aed; }

        /* ── Submit button ── */
        .btn-login {
            width: 100%;
            margin-top: 8px;
            padding: 13px;
            font-size: .95rem;
            border-radius: 12px;
            letter-spacing: .02em;
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            color: #fff;
            border: none;
            font-weight: 700;
            cursor: pointer;
            transition: all .22s ease;
            box-shadow: 0 6px 20px rgba(124,58,237,.4);
            font-family: inherit;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #6d28d9, #4c1d95);
            box-shadow: 0 8px 28px rgba(124,58,237,.6);
            transform: translateY(-1px);
        }
        .btn-login:active { transform: translateY(0); }

        /* ── Divider ── */
        .or-divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 22px 0 14px;
            font-size: .78rem;
            color: #d1d5db;
        }
        .or-divider::before, .or-divider::after {
            content: ''; flex: 1; height: 1px; background: #e5e7eb;
        }

        /* ── Bottom links ── */
        .auth-bottom { margin-top: 20px; text-align: center; }
        .auth-bottom p { font-size: .88rem; color: #64748b; margin: 6px 0; }
        .auth-bottom a { color: #7c3aed; font-weight: 600; text-decoration: none; }
        .auth-bottom a:hover { text-decoration: underline; }
        
        @media(max-width: 480px) {
            .auth-card { padding: 36px 20px; }
            .logo-icon-wrap { width: 60px; height: 60px; font-size: 1.6rem; }
            .auth-logo h1 { font-size: 1.3rem; }
            .trust-badges { gap: 6px; }
            .trust-badge { padding: 3px 10px; font-size: .68rem; }
        }
    </style>
</head>
<body>

<div class="auth-card">

    <!-- Logo -->
    <div class="auth-logo">
        <div class="logo-icon-wrap">🗳️</div>
        <h1>VoteSecure</h1>
        <p>Sign in to your voter account</p>
    </div>

    <!-- Trust Badges -->
    <div class="trust-badges">
        <span class="trust-badge">🆔 ID Verified</span>
        <span class="trust-badge">🔒 Encrypted</span>
        <span class="trust-badge">✅ Secure Vote</span>
    </div>

    <!-- Error Alert -->
    <?php if($login_error): ?>
        <div class="alert alert-danger">⚠️ <?php echo htmlspecialchars($login_error); ?></div>
    <?php endif; ?>

    <!-- Login Form -->
    <form method="POST" id="loginForm" novalidate>

        <div class="form-group">
            <label for="email">Email Address</label>
            <div class="input-icon-wrap">
                <span class="field-icon">📧</span>
                <input type="email" id="email" name="email"
                       placeholder="you@gmail.com" required autocomplete="email"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="password" style="display:flex;justify-content:space-between;align-items:center;">
                <span>Password</span>
                <a href="forgot_password.php" style="font-size:.78rem;font-weight:600;color:#7c3aed;">Forgot password?</a>
            </label>
            <div class="input-icon-wrap pass-wrap">
                <span class="field-icon">🔑</span>
                <input type="password" id="password" name="password"
                       placeholder="Enter your password" required autocomplete="current-password">
                <button type="button" class="toggle-pass" id="togglePass" title="Show / hide password">👁</button>
            </div>
        </div>

        <button type="submit" name="login" class="btn-login" id="loginBtn">
            Sign In →
        </button>
    </form>

    <div class="or-divider">or</div>

    <div class="auth-bottom">
        <p>Don't have an account? <a href="register.php">Register Here</a></p>
        <p><a href="../index.php">← Back to Home</a></p>
    </div>

    <!-- Security note -->
    <div style="margin-top:22px;padding:11px 14px;background:#f5f3ff;border-radius:10px;border:1px solid #e0d9f7;font-size:.75rem;color:#7c6fac;text-align:center;line-height:1.5;">
        🔐 Login uses your <strong>Email &amp; Password</strong>. Aadhar &amp; Mobile are stored for identity verification only.
    </div>
</div>

<script>
// Toggle password visibility
document.getElementById('togglePass').addEventListener('click', function(){
    const pwd = document.getElementById('password');
    const isHidden = pwd.type === 'password';
    pwd.type = isHidden ? 'text' : 'password';
    this.textContent = isHidden ? '🙈' : '👁';
});

// Button loading state on submit
document.getElementById('loginForm').addEventListener('submit', function(e){
    const btn = document.getElementById('loginBtn');
    // Delay disabling so the form POST fires first
    setTimeout(function(){
        btn.textContent = 'Signing in…';
        btn.disabled = true;
    }, 50);
    // Re-enable after 5s as fallback
    setTimeout(function(){
        btn.textContent = 'Sign In →';
        btn.disabled = false;
    }, 5000);
});
</script>

</body>
</html>
