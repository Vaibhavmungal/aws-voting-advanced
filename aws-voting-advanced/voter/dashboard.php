<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$query = mysqli_query($conn,"SELECT * FROM elections WHERE status='Active'");
?>

<!DOCTYPE html>
<html>
<head>
<title>Voter Dashboard</title>

<link rel="stylesheet" href="../assets/css/style.css">

<style>
body{
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
}

/* NAVBAR */
.navbar{
    background:#333;
    padding:10px;
    display:flex;
    gap:10px;
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

/* CARD */
.card{
    background:white;
    padding:20px;
    margin:20px;
    border-radius:10px;
    box-shadow:0 2px 10px rgba(0,0,0,0.2);
}
</style>

</head>

<body>

<!-- HEADER -->
<div class="top-header">
    <h3>Online Voting System</h3>
    <div>
        Welcome User |
        <a href="logout.php" style="color:red;">Logout</a>
    </div>
</div>

<!-- NAVBAR -->
<div class="navbar">
    <a href="dashboard.php">Home</a>
    <a href="feedback.php">Feedback</a>
</div>

<!-- CONTENT -->
<h2 style="text-align:center;">Available Elections</h2>

<?php while($row = mysqli_fetch_assoc($query)){ ?>

<div class="card">

<h3><?php echo $row['title']; ?></h3>

<p>Type: <?php echo $row['type']; ?></p>
<p>Ends: <?php echo $row['end_date']; ?></p>

<a href="vote.php?election=<?php echo $row['id']; ?>" 
style="background:#27ae60; color:white; padding:10px 15px; text-decoration:none;">
Vote Now
</a>

</div>

<?php } ?>

</body>
</html>