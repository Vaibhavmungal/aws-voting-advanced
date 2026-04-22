<?php
include("../config/database.php");

if(isset($_POST['register'])){

    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $allowed_domain = "@college.edu";  // change to real college domain

    // 1. Check College Email
    if(substr($email, -strlen($allowed_domain)) !== $allowed_domain){
        $error = "Only Official College Email Allowed!";
    }
    // 2. Check Duplicate Email
    elseif(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users WHERE email='$email'")) > 0){
        $error = "Email already registered!";
    }
    else {
        // 3. Hash password before storing
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        mysqli_query($conn,
            "INSERT INTO users (name, email, password, type)
             VALUES ('$name','$email','$hashed','College')"
        );

        $success = "Registered Successfully! You can now login.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Voter Registration</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">

<h2>Voter Registration</h2>

<?php if(isset($error))  echo "<p style='color:red;'>$error</p>"; ?>
<?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>

<form method="POST">
    Name:<br>
    <input name="name" required><br><br>

    Email:<br>
    <input type="email" name="email" required><br><br>

    Password:<br>
    <input type="password" name="password" required><br><br>

    <button name="register">Register</button>
</form>

<p>Already have an account? <a href="login.php">Login Here</a></p>

</div>
</body>
</html>
