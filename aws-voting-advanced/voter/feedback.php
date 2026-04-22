<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

if(isset($_POST['submit_feedback'])){

    $user_id = $_SESSION['user'];
    $message = $_POST['message'];

    mysqli_query($conn,"
    INSERT INTO feedback (user_id,message)
    VALUES ('$user_id','$message')
    ");

    $success = "Feedback submitted successfully!";
}
?>

<!DOCTYPE html>
<html>

<head>
<title>Submit Feedback</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="container">

<div class="card">

<h2>Submit Feedback</h2>
<hr>

<?php
if(isset($success)){
    echo "<p class='success'>$success</p>";
}
?>

<form method="POST">

<label>Your Feedback</label>

<br><br>

<textarea name="message" rows="5" required></textarea>

<br><br>

<button class="btn" name="submit_feedback">
Submit Feedback
</button>

</form>

<br>

<a href="dashboard.php" class="btn">Back</a>

</div>

</div>

</body>
</html>