<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

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
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='".mysqli_real_escape_string($conn, $email)."'");
        if(mysqli_num_rows($check) > 0){
            $error = "A voter with this email already exists.";
        } else {
            $query = "INSERT INTO users (name, email, password, has_voted, type)
                      VALUES ('".mysqli_real_escape_string($conn, $name)."',
                              '".mysqli_real_escape_string($conn, $email)."',
                              '".mysqli_real_escape_string($conn, $password)."',
                              0,
                              '".mysqli_real_escape_string($conn, $type)."')";
            if(mysqli_query($conn, $query)){
                $success = "Voter added successfully!";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Voter</title>
<style>
body { font-family: Arial; background: #f4f4f4; }

.container {
    max-width: 500px;
    margin: 40px auto;
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

h2 { margin-bottom: 20px; color: #333; }

label { display: block; margin-top: 15px; margin-bottom: 5px; font-weight: bold; color: #555; }

input, select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
    font-size: 14px;
}

.btn {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 20px;
    background: #27ae60;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 15px;
    text-decoration: none;
}

.btn:hover { background: #219150; }

.btn-back {
    background: #444;
    margin-left: 10px;
}

.btn-back:hover { background: #222; }

.success {
    background: #d4edda;
    color: #155724;
    padding: 10px 15px;
    border-radius: 5px;
    margin-bottom: 15px;
}

.error {
    background: #f8d7da;
    color: #721c24;
    padding: 10px 15px;
    border-radius: 5px;
    margin-bottom: 15px;
}
</style>
</head>
<body>

<div class="container">
    <h2>Add New Voter</h2>

    <?php if($success): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">

        <label>Full Name</label>
        <input type="text" name="name" placeholder="Enter full name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">

        <label>Email Address</label>
        <input type="email" name="email" placeholder="Enter email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

        <label>Password</label>
        <input type="text" name="password" placeholder="Set a password" required>

        <label>Voter Type</label>
        <select name="type">
            <option value="College">College</option>
            <option value="Faculty">Faculty</option>
            <option value="Staff">Staff</option>
        </select>

        <br>
        <button type="submit" class="btn">+ Add Voter</button>
        <a href="manage_voters.php" class="btn btn-back">← Back</a>

    </form>
</div>

</body>
</html>
