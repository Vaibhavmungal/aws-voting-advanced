<?php
session_start();
include("../config/database.php");
if(!isset($_SESSION['user'])) header("Location: login.php");
$user=$_SESSION['user'];
$check=mysqli_query($conn,"SELECT has_voted FROM users WHERE id='$user'");
$row=mysqli_fetch_assoc($check);
?>
<h2>Voter Dashboard</h2>
<?php if($row['has_voted']==0){ ?>
<a href="vote.php">Cast Vote</a>
<?php } else { echo "<p>You already voted.</p>"; } ?>
<br><a href="logout.php">Logout</a>
