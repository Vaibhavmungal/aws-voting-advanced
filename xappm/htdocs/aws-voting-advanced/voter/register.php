<?php
include("../config/database.php");

if(isset($_POST['register'])){

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $allowed_domain = "@college.edu";  // change to real college domain

    // 1️⃣ Check College Email
    if(substr($email, -strlen($allowed_domain)) !== $allowed_domain){
        echo "<p style='color:red;'>Only Official College Email Allowed!</p>";
        exit();
    }

    // 2️⃣ Check Duplicate Email
    $check = mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");
    if(mysqli_num_rows($check) > 0){
        echo "<p style='color:red;'>Email already registered!</p>";
        exit();
    }

    // 3️⃣ Insert User (Auto Type College)
    mysqli_query($conn,
        "INSERT INTO users (name,email,password,type)
         VALUES ('$name','$email','$password','College')");

    echo "<p style='color:green;'>Registered Successfully!</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Voter Registration</title>
</head>
<body>

<h2>Voter Registration</h2>

<form method="POST">
    Name:<br>
    <input name="name" required><br><br>

    Email:<br>
    <input type="email" name="email" required><br><br>

    Password:<br>
    <input type="password" name="password" required><br><br>

    <button name="register">Register</button>
</form>

</body>
</html>
