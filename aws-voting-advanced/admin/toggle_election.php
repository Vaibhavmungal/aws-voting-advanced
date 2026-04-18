<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

$id     = (int)$_GET['id'];
$status = ($_GET['status'] === 'Active') ? 'Active' : 'Inactive';

mysqli_query($conn, "UPDATE elections SET status='$status' WHERE id=$id");

header("Location: manage_elections.php");
exit();
?>
