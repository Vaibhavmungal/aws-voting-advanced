<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

/* ================= DELETE CANDIDATE ================= */
if(isset($_GET['delete'])){

    $id = (int)$_GET['delete'];

    $img      = mysqli_query($conn, "SELECT image FROM candidates WHERE id=$id");
    $img_data = mysqli_fetch_assoc($img);

    if($img_data && $img_data['image'] != ""){
        $img_path = "../uploads/" . $img_data['image'];
        if(file_exists($img_path)) unlink($img_path);
    }

    mysqli_query($conn, "DELETE FROM candidates WHERE id=$id");

    header("Location: manage_candidates.php");
    exit();
}

/* ================= UPDATE CANDIDATE ================= */
if(isset($_POST['update_candidate'])){

    $id       = (int)$_POST['id'];
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $position = mysqli_real_escape_string($conn, $_POST['position']);

    mysqli_query($conn,
        "UPDATE candidates SET name='$name', position='$position' WHERE id=$id"
    );

    header("Location: manage_candidates.php");
    exit();
}

/* ================= ADD CANDIDATE ================= */
if(isset($_POST['add_candidate'])){

    $name        = mysqli_real_escape_string($conn, $_POST['name']);
    $position    = mysqli_real_escape_string($conn, $_POST['position']);
    $election_id = (int)$_POST['election_id'];

    $image_name = $_FILES['image']['name'];
    $tmp_name   = $_FILES['image']['tmp_name'];
    $folder     = "../uploads/";

    if($image_name != ""){
        move_uploaded_file($tmp_name, $folder . $image_name);
    }

    mysqli_query($conn,
        "INSERT INTO candidates (name, position, election_id, image)
         VALUES ('$name','$position','$election_id','$image_name')"
    );

    header("Location: manage_candidates.php");
    exit();
}

$result = mysqli_query($conn, "SELECT c.*, e.title as election_title
                                FROM candidates c
                                JOIN elections e ON c.election_id = e.id");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Candidates</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="container">

<h2>Manage Candidates</h2>

<hr>

<!-- ================= ADD FORM ================= -->
<h3>Add New Candidate</h3>

<form method="POST" enctype="multipart/form-data">
    Name:<br>
    <input type="text" name="name" required><br><br>

    Position:<br>
    <input type="text" name="position" required><br><br>

    Election:<br>
    <select name="election_id" required>
        <option value="">Select Election</option>
        <?php
        $elections = mysqli_query($conn, "SELECT * FROM elections");
        while($e = mysqli_fetch_assoc($elections)){
        ?>
        <option value="<?php echo $e['id']; ?>">
            <?php echo htmlspecialchars($e['title']); ?>
        </option>
        <?php } ?>
    </select><br><br>

    Photo:<br>
    <input type="file" name="image"><br><br>

    <button name="add_candidate">Add Candidate</button>
</form>

<hr>

<!-- ================= LIST TABLE ================= -->
<h3>All Candidates</h3>

<div style="margin-bottom:10px;">
    <input type="text" id="searchCandidate" placeholder="Search..." style="padding:8px;">
</div>

<table border="1" cellpadding="10">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Position</th>
    <th>Election</th>
    <th>Photo</th>
    <th>Votes</th>
    <th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)){ ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo htmlspecialchars($row['name']); ?></td>
    <td><?php echo htmlspecialchars($row['position']); ?></td>
    <td><?php echo htmlspecialchars($row['election_title']); ?></td>
    <td>
        <?php if(!empty($row['image'])){ ?>
            <img src="../uploads/<?php echo $row['image']; ?>" width="80">
        <?php } else { echo "No image"; } ?>
    </td>
    <td>
        <?php
        $cid   = (int)$row['id'];
        $count = mysqli_query($conn, "SELECT COUNT(*) as total FROM votes WHERE candidate_id=$cid");
        $data  = mysqli_fetch_assoc($count);
        echo $data['total'];
        ?>
    </td>
    <td>
        <a href="?edit=<?php echo $row['id']; ?>">Edit</a>
        |
        <a href="?delete=<?php echo $row['id']; ?>"
           onclick="return confirm('Delete this candidate?')">Delete</a>
    </td>
</tr>
<?php } ?>
</table>

<hr>

<!-- ================= EDIT FORM ================= -->
<?php
if(isset($_GET['edit'])){
    $edit_id    = (int)$_GET['edit'];
    $edit_query = mysqli_query($conn, "SELECT * FROM candidates WHERE id=$edit_id");
    $edit_data  = mysqli_fetch_assoc($edit_query);
    if($edit_data){
?>
<h3>Edit Candidate</h3>
<form method="POST">
    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">

    Name:<br>
    <input type="text" name="name"
           value="<?php echo htmlspecialchars($edit_data['name']); ?>" required><br><br>

    Position:<br>
    <input type="text" name="position"
           value="<?php echo htmlspecialchars($edit_data['position']); ?>" required><br><br>

    <button name="update_candidate">Update Candidate</button>
</form>
<?php } } ?>

</div>

<script>
document.getElementById("searchCandidate").addEventListener("keyup", function() {
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
