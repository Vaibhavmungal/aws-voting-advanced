<?php
session_start();
include("../config/database.php");

if(isset($_SESSION['user'])){
    header("Location: dashboard.php");
    exit();
}

$error   = '';
$success = '';

if(isset($_POST['register'])){
    $name          = trim($_POST['name']          ?? '');
    $email         = trim($_POST['email']         ?? '');
    $election_card = trim($_POST['election_card'] ?? '');
    $mobile        = trim($_POST['mobile']        ?? '');
    $password      =      $_POST['password']      ?? '';
    $confirm_pass  =      $_POST['confirm_password'] ?? '';

    // ── Validation ──────────────────────────────────────────
    if(empty($name) || empty($email) || empty($election_card) || empty($mobile) || empty($password)){
        $error = "All fields are required.";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Please enter a valid email address.";
    } elseif(strlen($password) < 6){
        $error = "Password must be at least 6 characters.";
    } elseif($password !== $confirm_pass){
        $error = "Passwords do not match.";
    } elseif(!preg_match('/^[0-9]{10}$/', $mobile)){
        $error = "Mobile number must be exactly 10 digits.";
    } elseif(!preg_match('/^[0-9]{12}$/', $election_card)){
        $error = "Aadhar card number must be exactly 12 digits (numbers only)."; 
    } else {

        // ── Check duplicate email ───────────────────────────
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if(!$stmt){
            $error = "DB Error (email check): " . $conn->error;
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            $emailExists = ($stmt->num_rows > 0);
            $stmt->close();

            if($emailExists){
                $error = "This email is already registered.";
            } else {
                // ── Check duplicate Aadhar card ──────────────
                $stmt2 = $conn->prepare("SELECT id FROM users WHERE election_card = ?");
                if(!$stmt2){
                    $error = "DB Error (card check): " . $conn->error;
                } else {
                    $stmt2->bind_param("s", $election_card);
                    $stmt2->execute();
                    $stmt2->store_result();
                    $cardExists = ($stmt2->num_rows > 0);
                    $stmt2->close();

                    if($cardExists){
                        $error = "This Aadhar card number is already registered.";
                    } else {
                        // ── Insert new user ──────────────────
                        $hashed = password_hash($password, PASSWORD_DEFAULT);
                        $ins = $conn->prepare(
                            "INSERT INTO users (name, email, password, type, election_card, mobile)
                             VALUES (?, ?, ?, 'College', ?, ?)"
                        );
                        if(!$ins){
                            $error = "DB Error (insert prepare): " . $conn->error;
                        } else {
                            $ins->bind_param("sssss", $name, $email, $hashed, $election_card, $mobile);
                            if($ins->execute()){
                                $success = "Account created successfully! You can now sign in.";
                            } else {
                                $error = "Registration failed: " . $ins->error;
                            }
                            $ins->close();
                        }
                    }
                }
            }
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/voter.css">
    <style>
        body {
            background:
                radial-gradient(ellipse 80% 60% at 20% -10%, rgba(124,58,237,.55), transparent),
                radial-gradient(ellipse 50% 50% at 85% 90%,  rgba(245,158,11,.15), transparent),
                #0a0118;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 36px 16px;
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
            padding: 48px 44px;
            width: 100%;
            max-width: 520px;
            box-shadow: 0 32px 90px rgba(0,0,0,.55), 0 0 0 1px rgba(124,58,237,.14);
            position: relative;
            z-index: 1;
        }

        /* ── Logo ── */
        .auth-logo { text-align: center; margin-bottom: 32px; }
        .auth-logo .logo-icon { font-size: 3rem; display: block; }
        .auth-logo h1 { font-size: 1.55rem; font-weight: 800; color: #1e1b4b; margin-top: 10px; }
        .auth-logo p  { font-size: .85rem; color: #6b7280; margin-top: 4px; }

        /* ── Section divider ── */
        .section-label {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: #7c3aed;
            margin: 22px 0 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e0d9f7;
        }

        /* ── Two-column row ── */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }
        @media(max-width: 480px){ .form-row { grid-template-columns: 1fr; } }

        /* ── Security badge ── */
        .security-note {
            background: #f5f3ff;
            border: 1px solid #ddd6fe;
            border-radius: 10px;
            padding: 11px 14px;
            font-size: .8rem;
            color: #6d28d9;
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 20px;
        }
        .security-note span { margin-top: 1px; font-size: 1rem; flex-shrink: 0; }

        /* ── Password strength hint ── */
        .hint { font-size: .76rem; color: #9ca3af; margin-top: 4px; }

        .auth-card .btn-primary {
            width: 100%;
            margin-top: 8px;
            padding: 13px;
            font-size: .95rem;
            border-radius: 12px;
            letter-spacing: .02em;
        }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="auth-logo">
        <span class="logo-icon">📝</span>
        <h1>Create Voter Account</h1>
        <p>Register to participate in elections</p>
    </div>

    <?php if($error):   ?><div class="alert alert-danger">⚠️ <?php echo htmlspecialchars($error);   ?></div><?php endif; ?>
    <?php if($success): ?><div class="alert alert-success">✅ <?php echo htmlspecialchars($success); ?> <a href="login.php">Sign In →</a></div><?php endif; ?>

    <?php if(!$success): ?>
    <div class="security-note">
        <span>🔒</span>
        <div>Your <strong>Election Card / Aadhar</strong> and <strong>Mobile</strong> are stored securely and used only for identity verification by the admin.</div>
    </div>

    <form method="POST" novalidate>

        <!-- ── Personal Info ── -->
        <div class="section-label">Personal Information</div>
        <div class="form-row">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="e.g OM" required
                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="you@gmail.com" required
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
        </div>

        <!-- ── Identity Verification ── -->
        <div class="section-label">Identity Verification</div>
        <div class="form-row">
            <div class="form-group">
                <label for="election_card">Aadhar Card No.</label>
                <input type="text" id="election_card" name="election_card"
                       placeholder="12-digit Aadhar number" required maxlength="12"
                       pattern="[0-9]{12}" inputmode="numeric"
                       value="<?php echo htmlspecialchars($_POST['election_card'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="mobile">Mobile Number</label>
                <input type="tel" id="mobile" name="mobile"
                       placeholder="10-digit number" required maxlength="10"
                       pattern="[0-9]{10}"
                       value="<?php echo htmlspecialchars($_POST['mobile'] ?? ''); ?>">
            </div>
        </div>

        <!-- ── Security ── -->
        <div class="section-label">Set Password</div>
        <div class="form-row">
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Min. 6 characters" required>
                <div class="hint">At least 6 characters</div>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required>
            </div>
        </div>

        <button type="submit" name="register" class="btn btn-primary">
            Create Account →
        </button>
    </form>
    <?php endif; ?>

    <p class="text-center mt-4" style="font-size:.9rem;color:#64748b;">
        Already have an account? <a href="login.php" style="font-weight:600;">Sign In</a>
    </p>
</div>

<script>
// Only run field JS when the form is visible (not on success screen)
const elCard = document.getElementById('election_card');
const elMob  = document.getElementById('mobile');

if(elCard){
    // Only allow digits for Aadhar
    elCard.addEventListener('input', function(){
        this.value = this.value.replace(/\D/g,'').slice(0,12);
    });
}
if(elMob){
    // Only allow digits for mobile
    elMob.addEventListener('input', function(){
        this.value = this.value.replace(/\D/g,'').slice(0,10);
    });
}
</script>
</body>
</html>
