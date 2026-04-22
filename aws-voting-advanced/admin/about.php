<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
<title>About System</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="container">

<div class="card">

<h2>About Online Voting System</h2>
<hr>

<p>
This Online Voting System is designed to provide a secure,
fast and transparent digital election platform for college voting.
Students can vote online while administrators manage elections
and candidates easily.
</p>

</div>


<div class="card">

<h3>System Features</h3>

<ul>
<li>Create and manage elections</li>
<li>Add candidates with images</li>
<li>Secure voting system</li>
<li>Automatic vote counting</li>
<li>Winner detection</li>
<li>Student feedback system</li>

</ul>

</div>


<div class="card">

<h3>Developer Information</h3>

<p>
Name: <b>Vaibhav</b>
</p>

<p>
Project: <b>Online Voting System</b>
</p>

<p>
Technology: <b>PHP, MySQL, HTML, CSS</b>
</p>

</div>

<a href="dashboard.php" class="btn">Back to Dashboard</a>

</div>

</body>
</html>