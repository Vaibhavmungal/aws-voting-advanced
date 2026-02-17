<?php
session_start();
include("../config/database.php");

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $pass  = $_POST['password'];

    $q = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND password='$pass'");

    if(mysqli_num_rows($q) > 0){
        $user = mysqli_fetch_assoc($q);
        $_SESSION['user'] = $user['id'];
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<p style='color:red;'>Invalid Login</p>";
    }
}
?>

<h2>Voter Login</h2>

<form method="POST">
    Email:<br>
    <input type="email" name="email" required><br><br>

    Password:<br>
    <input type="password" name="password" required><br><br>

    <button name="login">Login</button>
</form>

<p>
Don't have an account?
<a href="register.php">Register Here</a>
</p>
