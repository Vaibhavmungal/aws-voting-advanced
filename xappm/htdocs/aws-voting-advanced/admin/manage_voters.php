<?php
include("../config/database.php");

/* ================= DELETE ================= */
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    header("Location: manage_voters.php");
    exit();
}

/* ================= UPDATE ================= */
if(isset($_POST['update_voter'])){
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    mysqli_query($conn, 
        "UPDATE users SET name='$name', email='$email' WHERE id=$id");

    header("Location: manage_voters.php");
    exit();
}

/* ================= ADD ================= */
if(isset($_POST['add_voter'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    mysqli_query($conn,
        "INSERT INTO users (name,email,password)
         VALUES('$name','$email','$password')");

    header("Location: manage_voters.php");
    exit();
}

$result = mysqli_query($conn,"SELECT * FROM users");
?>

<h2>Manage Voters</h2>

<h3>Add New Voter</h3>
<form method="POST">
    Name: <input type="text" name="name" required><br><br>
    Email: <input type="email" name="email" required><br><br>
    Password: <input type="text" name="password" required><br><br>
    <button name="add_voter">Add Voter</button>
</form>

<hr>

<table border="1" cellpadding="8">
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)){ ?>
<tr>
    <td><?php echo $row['name']; ?></td>
    <td><?php echo $row['email']; ?></td>
    <td>
        <a href="manage_voters.php?delete=<?php echo $row['id']; ?>"
           onclick="return confirm('Delete this voter?')">
           Delete
        </a>
    </td>
</tr>
<?php } ?>
</table>
