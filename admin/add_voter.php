<?php
session_start();
include("../config/database.php");

$page_title = "Add Voter";
include("includes/header.php");

$success = "";
$error = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name          = trim($_POST['name']);
    $email         = trim($_POST['email']);
    $password      = trim($_POST['password']);
    $type          = trim($_POST['type']);
    $election_card = trim($_POST['election_card'] ?? '');
    $mobile        = trim($_POST['mobile'] ?? '');

    // Normalise empties to NULL
    $election_card = $election_card !== '' ? $election_card : null;
    $mobile        = $mobile        !== '' ? $mobile        : null;

    if(empty($name) || empty($email) || empty($password)){
        $error = "Name, email and password are required.";
    } elseif($mobile !== null && !preg_match('/^[0-9]{10}$/', $mobile)){
        $error = "Mobile number must be exactly 10 digits.";
    } elseif($election_card !== null && !preg_match('/^[0-9]{12}$/', $election_card)){
        $error = "Aadhar card must be exactly 12 digits.";
    } else {
        // Check duplicate email
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->num_rows > 0){
            $error = "A voter with this email already exists.";
            $stmt->close();
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt->close();
            
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, has_voted, type, election_card, mobile) VALUES (?, ?, ?, 0, ?, ?, ?)");
            $stmt->bind_param("ssssss", $name, $email, $hashed, $type, $election_card, $mobile);
            
            if($stmt->execute()){
                $success = "Voter added successfully!";
                $action = "Admin added a new voter: $email";
                $log_stmt = $conn->prepare("INSERT INTO logs (action) VALUES (?)");
                $log_stmt->bind_param("s", $action);
                $log_stmt->execute();
                $log_stmt->close();
            } else {
                $error = "Something went wrong: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<h1 class="page-title">➕ Add New Voter</h1>
<p class="page-subtitle">Manually register a new voter into the system.</p>

<div class="card" style="max-width: 600px;">
    <?php if($success): ?>
        <div class="alert alert-success">✅ <?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if($error): ?>
        <div class="alert alert-danger">⚠️ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Full Name <span style="color:#ef4444;">*</span></label>
            <input type="text" name="name" placeholder="Enter full name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
        </div>

        <div class="form-group">
            <label>Email Address <span style="color:#ef4444;">*</span></label>
            <input type="email" name="email" placeholder="Enter email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>

        <div class="form-group">
            <label>Password <span style="color:#ef4444;">*</span></label>
            <input type="text" name="password" placeholder="Set a password" required>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div class="form-group">
                <label>Aadhar / ID Card No. <small style="color:#9ca3af;">(optional, 12 digits)</small></label>
                <input type="text" name="election_card" placeholder="12-digit number" maxlength="12" inputmode="numeric"
                       value="<?php echo isset($_POST['election_card']) ? htmlspecialchars($_POST['election_card']) : ''; ?>">
            </div>
            <div class="form-group">
                <label>Mobile Number <small style="color:#9ca3af;">(optional, 10 digits)</small></label>
                <input type="tel" name="mobile" placeholder="10-digit number" maxlength="10"
                       value="<?php echo isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : ''; ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Voter Type</label>
            <select name="type">
                <option value="College">College Student</option>
                <option value="Faculty">Faculty</option>
                <option value="Staff">Staff</option>
                <option value="NGO">NGO Member</option>
                <option value="Member">General Member</option>
            </select>
        </div>

        <div style="margin-top: 24px;">
            <button type="submit" class="btn btn-primary">➕ Add Voter</button>
            <a href="manage_voters.php" class="btn btn-outline" style="margin-left: 8px;">Cancel</a>
        </div>
    </form>
</div>

<?php include("includes/footer.php"); ?>
