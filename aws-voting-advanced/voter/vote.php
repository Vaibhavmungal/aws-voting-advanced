<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

if(!isset($_GET['election'])){
    die("Election ID missing!");
}

$user_id     = (int)$_SESSION['user'];
$election_id = (int)$_GET['election'];

// Check if already voted in THIS election
$check = mysqli_query($conn,
    "SELECT * FROM votes
     WHERE user_id='$user_id' AND election_id='$election_id'"
);

if(mysqli_num_rows($check) > 0){
    header("Location: already_voted.php");
    exit();
}

// Get candidates for this election
$candidates = mysqli_query($conn,
    "SELECT * FROM candidates WHERE election_id='$election_id'"
);

// Submit vote
if(isset($_POST['vote'])){

    if(!isset($_POST['candidate'])){
        die("Please select a candidate!");
    }

    $candidate_id = (int)$_POST['candidate'];

    // Insert vote record
    mysqli_query($conn,
        "INSERT INTO votes (user_id, candidate_id, election_id)
         VALUES ('$user_id','$candidate_id','$election_id')"
    );

    // ✅ FIX: Update has_voted flag in users table
    mysqli_query($conn,
        "UPDATE users SET has_voted=1 WHERE id='$user_id'"
    );

    header("Location: vote_success.php?election=$election_id&candidate=$candidate_id");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Vote</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="voter-bg">

<div class="container">
<div class="card">

<h2>Select Candidate</h2>

<form method="POST">

<?php
if(mysqli_num_rows($candidates) == 0){
    echo "<h3>No candidates available for this election.</h3>";
} else {
    while($row = mysqli_fetch_assoc($candidates)){
?>

<div class="candidate">
<label>
    <input type="radio" name="candidate" value="<?php echo $row['id']; ?>">

    <?php if(!empty($row['image'])){ ?>
        <img src="../uploads/<?php echo $row['image']; ?>" width="80">
    <?php } ?>

    <?php echo htmlspecialchars($row['name']); ?>
    (<?php echo htmlspecialchars($row['position']); ?>)
</label>
</div>

<?php } } ?>

<br>
<button class="btn vote-btn" name="vote">Submit Vote</button>

</form>

</div>
</div>

</body>
</html>
