<?php
include("../config/database.php");

$result = mysqli_query($conn,"SELECT * FROM feedback ORDER BY id DESC");
?>

<h2>Feedback Reviews</h2>

<table border="1" cellpadding="8">
<tr>
    <th>Name</th>
    <th>Message</th>
    <th>Date</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)){ ?>
<tr>
    <td><?php echo $row['name']; ?></td>
    <td><?php echo $row['message']; ?></td>
    <td><?php echo $row['created_at']; ?></td>
</tr>
<?php } ?>
</table>
