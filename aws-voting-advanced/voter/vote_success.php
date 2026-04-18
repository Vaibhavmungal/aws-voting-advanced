<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user'];
$election_id = $_GET['election'];

/* Get Election Name */
$election_query = mysqli_query($conn,
"SELECT title FROM elections WHERE id='$election_id'");
$election = mysqli_fetch_assoc($election_query);

/* Get Last Voted Candidate */
$vote_query = mysqli_query($conn,
"SELECT c.name 
 FROM votes v
 JOIN candidates c ON v.candidate_id = c.id
 WHERE v.user_id='$user_id'
 AND v.election_id='$election_id'
 ORDER BY v.id DESC
 LIMIT 1
");

$candidate = mysqli_fetch_assoc($vote_query);
?>

<h2 style="color:green;">Vote Submitted Successfully!</h2>

<p><strong>Election:</strong> 
<?php echo $election['title']; ?>
</p>

<p><strong>You Voted For:</strong> 
<?php 
if($candidate){
    echo $candidate['name'];
}else{
    echo "Not Found";
}
?>
</p>

<a href="dashboard.php">Go to Dashboard</a>
