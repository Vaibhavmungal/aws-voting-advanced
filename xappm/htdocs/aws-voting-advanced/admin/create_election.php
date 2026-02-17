<?php
include("../config/database.php");
session_start();

if(isset($_POST['create'])){
$title=$_POST['title'];
$type=$_POST['type'];
$start=$_POST['start'];
$end=$_POST['end'];
$status=$_POST['status'];

mysqli_query($conn,"INSERT INTO elections 
(title,type,start_date,end_date,status)
VALUES('$title','$type','$start','$end','$status')");

echo "Election Created!";
}
?>

<h2>Create Election</h2>

<form method="POST">
Title:<br>
<input name="title"><br><br>

Type:<br>
<select name="type">
<option>College</option>
<option>School</option>
<option>Club</option>
<option>Organization</option>
</select><br><br>

Start Date:<br>
<input type="datetime-local" name="start"><br><br>

End Date:<br>
<input type="datetime-local" name="end"><br><br>

Status:<br>
<select name="status">
<option>Active</option>
<option>Inactive</option>
</select><br><br>

<button name="create">Create</button>
</form>
