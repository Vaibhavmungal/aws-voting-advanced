<?php
session_start();
include("../config/database.php");
$user=$_SESSION['user'];
if(isset($_POST['vote'])){
$candidate=$_POST['candidate'];
mysqli_query($conn,"INSERT INTO votes(user_id,candidate_id) VALUES('$user','$candidate')");
mysqli_query($conn,"UPDATE users SET has_voted=1 WHERE id='$user'");
echo "Vote Submitted!";
}
$res=mysqli_query($conn,"SELECT * FROM candidates");
?>
<form method="POST">
<select name="candidate">
<?php while($r=mysqli_fetch_assoc($res)){ ?>
<option value="<?php echo $r['id']; ?>"><?php echo $r['name']; ?></option>
<?php } ?>
</select><br><br>
<button name="vote">Vote</button>
</form>
