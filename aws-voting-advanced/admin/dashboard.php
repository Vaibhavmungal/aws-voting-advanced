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
<title>Admin Dashboard</title>

<link rel="stylesheet" href="../assets/css/style.css">

<style>
body {
    margin:0;
    font-family: Arial;
    background:#f4f4f4;
}

/* HEADER */
.top-header{
    background:#222;
    color:white;
    padding:10px 20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.top-header h2{
    margin:0;
    font-size:18px;
}

/* NAVBAR */
.navbar{
    background:#333;
    padding:10px;
    display:flex;
    gap:10px;
    align-items:center;
}

.navbar a{
    color:white;
    text-decoration:none;
    padding:8px 12px;
    background:#444;
    border-radius:5px;
}

.navbar a:hover{
    background:#27ae60;
}

/* CONTENT */
.container{
    padding:20px;
}

/* CARDS */
.card{
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 2px 10px rgba(0,0,0,0.2);
    margin-bottom:20px;
}
</style>

</head>

<body>

<!-- HEADER -->
<div class="top-header">
    <h2>Online Voting System</h2>
    <div>
        Welcome: <b>Admin</b> |
        <a href="logout.php" style="color:red;">Logout</a>
    </div>
</div>

<!-- NAVBAR -->
<div class="navbar">
    <a href="dashboard.php">Home</a>
    <a href="manage_candidates.php">Candidates List</a>
    <a href="manage_voters.php">Voters List</a>
    <a href="manage_elections.php">Manage & Create Elections</a>
    <a href="results.php">Results</a>
    <a href="feedback.php">Feedback</a>
    <a href="about.php">About</a>
</div>

<!-- CONTENT -->
<div class="container">

<div class="card">
    <h2>Admin Dashboard</h2>
    <p>Manage elections, candidates, voters and results.</p>
</div>

<div class="card">
    <h3>Quick Actions</h3>
    <a href="create_election.php">➕ Create Election</a><br><br>
    <a href="manage_elections.php">📋 Manage & Create Elections</a><br><br>
    <a href="manage_candidates.php">👤 Candidates</a><br><br>
    <a href="manage_voters.php">🧑‍🎓 Voters</a><br><br>
    <a href="results.php">📊 Results</a>
</div>

</div>

</body>
</html>