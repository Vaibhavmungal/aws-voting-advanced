<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=voters.xls");

$result = mysqli_query($conn, "SELECT * FROM users");

echo "ID\tName\tEmail\tStatus\n";

while($row = mysqli_fetch_assoc($result)){
    $status = ($row['has_voted'] == 1) ? "Voted" : "Not Voted";
    echo $row['id'] . "\t" . $row['name'] . "\t" . $row['email'] . "\t" . $status . "\n";
}
?>
