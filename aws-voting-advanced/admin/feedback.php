<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

$result = mysqli_query($conn,
    "SELECT f.message, f.created_at, u.name
     FROM feedback f
     JOIN users u ON f.user_id = u.id
     ORDER BY f.id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Feedback Reviews</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">

<h2>Feedback Reviews</h2>

<table border="1" cellpadding="10">
<tr>
    <th>Name</th>
    <th>Message</th>
    <th>Date</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)){ ?>
<tr>
    <td><?php echo htmlspecialchars($row['name']); ?></td>
    <td><?php echo htmlspecialchars($row['message']); ?></td>
    <td><?php echo $row['created_at']; ?></td>
</tr>
<?php } ?>
</table>

<br>
<a href="dashboard.php">Back to Dashboard</a>

</div>
</body>
</html>
