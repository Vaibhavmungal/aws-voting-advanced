<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

if(isset($_POST['create'])){
    $title  = mysqli_real_escape_string($conn, $_POST['title']);
    $type   = $_POST['type'];
    $start  = $_POST['start'];
    $end    = $_POST['end'];
    $status = $_POST['status'];

    mysqli_query($conn,
        "INSERT INTO elections (title, type, start_date, end_date, status)
         VALUES ('$title','$type','$start','$end','$status')"
    );

    header("Location: manage_elections.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Create Election</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">

<h2>Create Election</h2>

<form method="POST">
    Title:<br>
    <input type="text" name="title" required><br><br>

    Type:<br>
    <select name="type">
        <option>College</option>
        <option>School</option>
        <option>Club</option>
        <option>Organization</option>
    </select><br><br>

    Start Date:<br>
    <input type="date" name="start" required><br><br>

    End Date:<br>
    <input type="date" name="end" required><br><br>

    Status:<br>
    <select name="status">
        <option>Active</option>
        <option>Inactive</option>
    </select><br><br>

    <button name="create">Create</button>
</form>

<br>
<a href="manage_elections.php">Back to Elections</a>

</div>
</body>
</html>
