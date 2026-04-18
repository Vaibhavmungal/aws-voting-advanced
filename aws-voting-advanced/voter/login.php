<?php
session_start();
include("../config/database.php");

if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Fetch the user record first, then verify password
    $q    = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($q);

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
<html>
<head>
<title>Voter Login</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">

<h2>Voter Login</h2>

<?php if(isset($login_error)) echo "<p style='color:red;'>$login_error</p>"; ?>

<form method="POST">
    Email:<br>
    <input type="email" name="email" required><br><br>

    Password:<br>
    <input type="password" name="password" required><br><br>

    <button name="login">Login</button>
</form>

<p>Don't have an account? <a href="register.php">Register Here</a></p>

</div>
</body>
</html>
