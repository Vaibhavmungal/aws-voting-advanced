<?php
include("../config/database.php");

$id = $_GET['id'];
$status = $_GET['status'];

mysqli_query($conn, "UPDATE elections SET status='$status' WHERE id=$id");

header("Location: manage_elections.php");
?>
