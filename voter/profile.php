<?php
session_start();
include("../config/database.php");

$page_title = "My Profile";
include("includes/header.php");

$uid = (int)$_SESSION['user'];
$success = "";
$error = "";

// Handle password change
if(isset($_POST['update_password'])){
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if(empty($current_password) || empty($new_password) || empty($confirm_password)){
        $error = "All fields are required.";
    } elseif($new_password !== $confirm_password){
        $error = "New passwords do not match.";
    } elseif(strlen($new_password) < 6){
        $error = "New password must be at least 6 characters long.";
    } else {
        // Verify current password
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $user_data = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // Support plain text legacy passwords or hashed passwords
        $valid = false;
        if(password_verify($current_password, $user_data['password'])){
            $valid = true;
        } elseif($user_data['password'] === $current_password) {
            $valid = true; // Legacy fallback
        }

        if($valid){
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed, $uid);
            if($stmt->execute()){
                $success = "Password updated successfully!";
            } else {
                $error = "Failed to update password.";
            }
            $stmt->close();
        } else {
            $error = "Current password is incorrect.";
        }
    }
}

// Fetch user info for display
$stmt = $conn->prepare("SELECT name, email, type, created_at, has_voted FROM users WHERE id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$me = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<h1 class="page-title">👤 My Profile</h1>
<p class="page-subtitle">Manage your account settings and password.</p>

<div style="display:flex; gap: 24px; flex-wrap: wrap; align-items: flex-start;">
    
    <!-- Profile Info Card -->
    <div class="card" style="flex: 1; min-width: 300px;">
        <div class="card-title">Account Details</div>
        
        <div style="margin-bottom: 16px;">
            <label style="font-size:.85rem; font-weight:600; color:var(--text-muted);">Full Name</label>
            <div style="font-size: 1.05rem; font-weight: 500; color:var(--text-main); margin-top: 4px;"><?php echo htmlspecialchars($me['name']); ?></div>
        </div>
        
        <div style="margin-bottom: 16px;">
            <label style="font-size:.85rem; font-weight:600; color:var(--text-muted);">Email Address</label>
            <div style="font-size: 1.05rem; font-weight: 500; color:var(--text-main); margin-top: 4px;"><?php echo htmlspecialchars($me['email']); ?></div>
        </div>

        <div style="margin-bottom: 16px;">
            <label style="font-size:.85rem; font-weight:600; color:var(--text-muted);">Voter Type</label>
            <div style="margin-top: 4px;">
                <span class="badge" style="background:#e0e7ff; color:#3730a3;"><?php echo htmlspecialchars($me['type']); ?></span>
            </div>
        </div>

        <div>
            <label style="font-size:.85rem; font-weight:600; color:var(--text-muted);">Voting Status</label>
            <div style="margin-top: 4px;">
                <?php if($me['has_voted']): ?>
                    <span class="badge" style="background:#dcfce7; color:#16a34a;">✅ Voted</span>
                <?php else: ?>
                    <span class="badge" style="background:#fef3c7; color:#d97706;">⏳ Not Voted</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Password Change Card -->
    <div class="card" style="flex: 1.5; min-width: 300px;">
        <div class="card-title">Change Password</div>
        
        <?php if($success): ?>
            <div class="alert alert-success">✅ <?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="alert alert-danger">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" required placeholder="Enter current password">
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" required placeholder="Min. 6 characters">
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" required placeholder="Type new password again">
                </div>
            </div>
            
            <button type="submit" name="update_password" class="btn btn-primary" style="margin-top: 8px;">
                🔐 Update Password
            </button>
        </form>
    </div>
    
</div>

<?php include("includes/footer.php"); ?>
