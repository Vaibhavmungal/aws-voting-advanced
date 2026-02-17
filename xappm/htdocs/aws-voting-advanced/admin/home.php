<?php
session_start();
include("../config/database.php");

// Optional: protect admin page
// if(!isset($_SESSION['admin'])){
//     header("Location: login.php");
//     exit();
// }

$total_elections = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM elections"));
$total_voters = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM users"));
$total_votes = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM votes"));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>

<h2>Admin Dashboard</h2>

<hr>

<p><strong>Total Elections:</strong> <?php echo $total_elections; ?></p>
<p><strong>Total Voters:</strong> <?php echo $total_voters; ?></p>
<p><strong>Total Votes:</strong> <?php echo $total_votes; ?></p>

<hr>

<h3>Admin Controls</h3>

<ul>
    <li><a href="create_election.php">Create Election</a></li>
    <li><a href="manage_elections.php">Manage Elections</a></li>
    <li><a href="manage_candidates.php">Manage Candidates</a></li>
    <li><a href="manage_voters.php">Manage Voters</a></li>
    <li><a href="feedback.php">View Feedback</a></li>
    <li><a href="results.php">View Results</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>

</body>
</html>
