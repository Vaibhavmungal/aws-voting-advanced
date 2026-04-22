<?php
session_start();
include("../config/database.php");

$elections=mysqli_query($conn,"SELECT * FROM elections");
?>

<!DOCTYPE html>
<html>

<head>
<title>Results</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="admin-bg">

<div class="container">

<h2>Election Results</h2>

<?php
while($election=mysqli_fetch_assoc($elections)){

echo "<div class='card'>";

echo "<h3>".$election['title']."</h3>";

$candidates=mysqli_query($conn,"
SELECT c.id,c.name,COUNT(v.id) as votes
FROM candidates c
LEFT JOIN votes v
ON c.id=v.candidate_id
WHERE c.election_id='".$election['id']."'
GROUP BY c.id
ORDER BY votes DESC
");

$winner="";
$max=0;

while($row=mysqli_fetch_assoc($candidates)){

echo $row['name']." - ".$row['votes']." votes<br>";

if($row['votes']>$max){
$max=$row['votes'];
$winner=$row['name'];
}

}

if($max>0){
echo "<p class='winner'>Winner: $winner</p>";
}

echo "</div>";

}
?>

</div>

</body>
</html>