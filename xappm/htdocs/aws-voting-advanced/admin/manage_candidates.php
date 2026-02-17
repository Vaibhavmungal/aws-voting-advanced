<?php
include("../config/database.php");

/* =============================
   UPDATE CANDIDATE
============================= */
if(isset($_POST['update_candidate'])) {

    $id = $_POST['id'];
    $name = $_POST['name'];
    $position = $_POST['position'];

    mysqli_query($conn,
        "UPDATE candidates 
         SET name='$name', position='$position' 
         WHERE id=$id");

    header("Location: manage_candidates.php");
    exit();
}

/* =============================
   DELETE CANDIDATE
============================= */
if(isset($_GET['delete'])) {

    $id = $_GET['delete'];

    // get image
    $result = mysqli_query($conn, "SELECT image FROM candidates WHERE id=$id");
    $data = mysqli_fetch_assoc($result);

    if(!empty($data['image'])) {
        unlink("../uploads/".$data['image']);
    }

    mysqli_query($conn, "DELETE FROM candidates WHERE id=$id");

    header("Location: manage_candidates.php");
    exit();
}

/* =============================
   ADD CANDIDATE
============================= */
if(isset($_POST['add_candidate'])) {

    $name = $_POST['name'];
    $position = $_POST['position'];
    $election_id = $_POST['election_id'];

    $image_name = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];

    $folder = "../uploads/";
    move_uploaded_file($tmp_name, $folder.$image_name);

    mysqli_query($conn,
        "INSERT INTO candidates (name, position, election_id, image)
         VALUES ('$name','$position','$election_id','$image_name')");

    header("Location: manage_candidates.php");
    exit();
}
?>

<h2>Manage Candidates</h2>

<!-- ================= ADD FORM ================= -->
<form method="POST" enctype="multipart/form-data">
    Name: <input type="text" name="name" required><br><br>
    Position: <input type="text" name="position" required><br><br>
    Election ID: <input type="number" name="election_id" required><br><br>
    Photo: <input type="file" name="image" required><br><br>
    <button name="add_candidate">Add Candidate</button>
</form>

<hr>

<!-- ================= LIST TABLE ================= -->
<table border="1" cellpadding="10">
<tr>
    <th>Name</th>
    <th>Position</th>
    <th>Photo</th>
    <th>Actions</th>
</tr>

<?php
$result = mysqli_query($conn, "SELECT * FROM candidates");

while($row = mysqli_fetch_assoc($result)) {
?>

<tr>
    <td><?php echo $row['name']; ?></td>
    <td><?php echo $row['position']; ?></td>
    <td>
        <img src="../uploads/<?php echo $row['image']; ?>" width="80">
    </td>
    <td>
        <a href="manage_candidates.php?edit=<?php echo $row['id']; ?>">Edit</a> |
        <a href="manage_candidates.php?delete=<?php echo $row['id']; ?>"
           onclick="return confirm('Are you sure?')">Delete</a>
    </td>
</tr>

<?php } ?>
</table>

<?php
/* ================= EDIT FORM ================= */
if(isset($_GET['edit'])) {

    $edit_id = $_GET['edit'];
    $edit_query = mysqli_query($conn, "SELECT * FROM candidates WHERE id=$edit_id");
    $edit_data = mysqli_fetch_assoc($edit_query);
?>

<hr>
<h3>Edit Candidate</h3>

<form method="POST">
    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">

    Name:
    <input type="text" name="name"
           value="<?php echo $edit_data['name']; ?>" required><br><br>

    Position:
    <input type="text" name="position"
           value="<?php echo $edit_data['position']; ?>" required><br><br>

    <button name="update_candidate">Update</button>
</form>

<?php } ?>
