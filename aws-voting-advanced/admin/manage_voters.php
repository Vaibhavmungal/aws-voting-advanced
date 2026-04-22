<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

// FILTER LOGIC
$filter = $_GET['filter'] ?? 'all';

if($filter == 'voted'){
    $query = "SELECT * FROM users WHERE has_voted=1";
}
elseif($filter == 'unvoted'){
    $query = "SELECT * FROM users WHERE has_voted=0";
}
else{
    $query = "SELECT * FROM users";
}

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Voters</title>

<style>
body { font-family: Arial; background:#f4f4f4; }

.container { padding:20px; }

.btn {
    padding:8px 12px;
    background:#444;
    color:white;
    text-decoration:none;
    border-radius:5px;
    margin-right:5px;
}

.btn:hover { background:#27ae60; }

.btn-green { background:#2ecc71; }

table {
    width:100%;
    border-collapse:collapse;
    background:white;
}

table th, table td {
    padding:10px;
    border:1px solid #ccc;
}
</style>
</head>

<body>

<div class="container">

<h2>Voters List</h2>

<!-- TOP BAR -->
<div style="display:flex; justify-content:space-between; margin-bottom:15px;">

<div>
<a href="?filter=all" class="btn">All</a>
<a href="?filter=voted" class="btn">Voted</a>
<a href="?filter=unvoted" class="btn">Unvoted</a>
<a href="add_voter.php" class="btn btn-green">+ Add Voter</a>
</div>

<div>
<input type="text" id="searchBox" placeholder="Search..." style="padding:8px;">
<a href="export.php" class="btn btn-green">Download Excel</a>
</div>

</div>

<!-- TABLE -->
<table>
<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Status</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)){ ?>

<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['email']; ?></td>
<td>
<?php echo ($row['has_voted']==1) ? "Voted" : "Not Voted"; ?>
</td>
</tr>

<?php } ?>

</table>

</div>

<!-- SEARCH SCRIPT -->
<script>
document.getElementById("searchBox").addEventListener("keyup", function() {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll("table tr");

    rows.forEach((row, index) => {
        if(index === 0) return;
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(value) ? "" : "none";
    });
});
</script>

</body>
</html>