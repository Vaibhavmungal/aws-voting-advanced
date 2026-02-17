<?php
session_start();
include("../config/database.php");
$current_time = date("Y-m-d H:i:s");

// Auto close expired elections
mysqli_query($conn, "
UPDATE elections 
SET status='Inactive'
WHERE status='Active' 
AND end_date < '$current_time'
");

$total_elections = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM elections"));
$total_active = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM elections WHERE status='Active'"));
$total_voters = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM users"));
$total_candidates = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM candidates"));
$total_votes = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM votes"));

$current_time = date("Y-m-d H:i:s");

$election_status_query = mysqli_query($conn,"
SELECT *,
CASE
    WHEN status='Inactive' THEN 'Inactive'
    WHEN status='Active' AND '$current_time' < start_date THEN 'Upcoming'
    WHEN status='Active' AND '$current_time' BETWEEN start_date AND end_date THEN 'Running'
    WHEN status='Active' AND '$current_time' > end_date THEN 'Expired'
END as live_status
FROM elections
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - AWS Voting</title>
    <style>
        body { font-family: Arial; background:#f4f6f9; }
        .box {
            display:inline-block;
            width:220px;
            margin:15px;
            padding:20px;
            background:white;
            border-radius:8px;
            box-shadow:0 2px 8px rgba(0,0,0,0.1);
            text-align:center;
        }
        .menu a {
            display:block;
            padding:10px;
            margin:8px 0;
            background:#007bff;
            color:white;
            text-decoration:none;
            border-radius:5px;
        }
        .menu a:hover {
            background:#0056b3;
        }
        h2 { text-align:center; }
        table { background:white; margin-top:20px; }
    </style>
</head>
<body>

<h2>Admin Dashboard - AWS Voting System</h2>
<hr>

<div class="box">
    <h3><?php echo $total_elections; ?></h3>
    <p>Total Elections</p>
</div>

<div class="box">
    <h3><?php echo $total_active; ?></h3>
    <p>Active Elections</p>
</div>

<div class="box">
    <h3><?php echo $total_voters; ?></h3>
    <p>Total Voters</p>
</div>

<div class="box">
    <h3><?php echo $total_candidates; ?></h3>
    <p>Total Candidates</p>
</div>

<div class="box">
    <h3><?php echo $total_votes; ?></h3>
    <p>Total Votes</p>
</div>

<hr>

<h3>Live Election Status</h3>

<table border="1" cellpadding="10">
<tr>
    <th>Title</th>
    <th>Type</th>
    <th>Status</th>
</tr>

<?php while($row = mysqli_fetch_assoc($election_status_query)) { ?>
<tr>
    <td><?php echo $row['title']; ?></td>
    <td><?php echo $row['type']; ?></td>
    <td>
        <?php
        if($row['live_status'] == 'Running'){
            echo "<span style='color:green;font-weight:bold;'>🟢 Running</span>";
        }
        elseif($row['live_status'] == 'Upcoming'){
            echo "<span style='color:orange;font-weight:bold;'>⏳ Upcoming</span>";
        }
        elseif($row['live_status'] == 'Expired'){
            echo "<span style='color:red;font-weight:bold;'>⛔ Expired</span>";
        }
        else{
            echo "<span style='color:gray;'>Inactive</span>";
        }
        ?>
    </td>
</tr>
<?php } ?>
</table>

<hr>

<h3>Admin Controls</h3>

<div class="menu">
    <a href="create_election.php">➕ Create New Election</a>
    <a href="manage_elections.php">🗂 Manage Elections</a>
    <a href="manage_candidates.php">👤 Manage Candidates</a>
    <a href="manage_voters.php">👥 Manage Voters</a>
    <a href="results.php">📊 View Results</a>
    <a href="feedback.php">💬 Feedback Reviews</a>
    <a href="../index.php">🏠 Home</a>
    <a href="logout.php" style="background:red;">🚪 Logout</a>
</div>

</body>
</html>
