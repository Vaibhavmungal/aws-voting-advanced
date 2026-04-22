<?php
session_start();
include("../config/database.php");

$page_title = "Add Voter";
include("includes/header.php");

$success = "";
$error = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $type     = trim($_POST['type']);

    if(empty($name) || empty($email) || empty($password)){
        $error = "All fields are required.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->num_rows > 0){
            $error = "A voter with this email already exists.";
            $stmt->close();
        } else {
            // Hash password
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt->close();
            
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, has_voted, type) VALUES (?, ?, ?, 0, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashed, $type);
            
            if($stmt->execute()){
                $success = "Voter added successfully!";
                
                // Add log
                $action = "Admin added a new voter: $email";
                $log_stmt = $conn->prepare("INSERT INTO logs (action) VALUES (?)");
                $log_stmt->bind_param("s", $action);
                $log_stmt->execute();
                $log_stmt->close();
            } else {
                $error = "Something went wrong. Please try again.";
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
            <label>Full Name</label>
            <input type="text" name="name" placeholder="Enter full name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="Enter email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="text" name="password" placeholder="Set a password" required>
        </div>

        <div class="form-group">
            <label>Voter Type</label>
            <select name="type">
                <option value="College">College</option>
                <option value="Faculty">Faculty</option>
                <option value="Staff">Staff</option>
            </select>
        </div>

        <div style="margin-top: 24px;">
            <button type="submit" class="btn btn-primary">➕ Add Voter</button>
            <a href="manage_voters.php" class="btn btn-outline" style="margin-left: 8px;">Cancel</a>
        </div>
    </form>
</div>

<?php include("includes/footer.php"); ?>
