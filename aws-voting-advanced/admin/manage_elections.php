<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

/* ================= CREATE ELECTION ================= */
if(isset($_POST['create_election'])){
    $title  = mysqli_real_escape_string($conn, $_POST['title']);
    $type   = mysqli_real_escape_string($conn, $_POST['type']);
    $start  = $_POST['start'];
    $end    = $_POST['end'];
    $status = $_POST['status'];

    mysqli_query($conn,
        "INSERT INTO elections (title, type, start_date, end_date, status)
         VALUES ('$title','$type','$start','$end','$status')"
    );

    header("Location: manage_elections.php");
    exit();
}

/* ================= UPDATE ELECTION ================= */
if(isset($_POST['update_election'])){
    $id     = (int)$_POST['id'];
    $title  = mysqli_real_escape_string($conn, $_POST['title']);
    $type   = mysqli_real_escape_string($conn, $_POST['type']);
    $start  = $_POST['start'];
    $end    = $_POST['end'];
    $status = $_POST['status'];

    mysqli_query($conn,
        "UPDATE elections
         SET title='$title', type='$type',
             start_date='$start', end_date='$end', status='$status'
         WHERE id=$id"
    );

    header("Location: manage_elections.php");
    exit();
}

/* ================= DELETE ELECTION ================= */
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM elections WHERE id=$id");
    header("Location: manage_elections.php");
    exit();
}

/* ================= ✅ TOGGLE START / STOP ================= */
if(isset($_GET['toggle'])){
    $id = (int)$_GET['toggle'];
    $current = mysqli_fetch_assoc(mysqli_query($conn, "SELECT status FROM elections WHERE id=$id"));
    $new_status = ($current['status'] == 'Active') ? 'Inactive' : 'Active';
    mysqli_query($conn, "UPDATE elections SET status='$new_status' WHERE id=$id");
    header("Location: manage_elections.php");
    exit();
}

$elections = mysqli_query($conn, "SELECT * FROM elections ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Elections</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
.btn-start {
    background: #27ae60;
    color: white;
    padding: 5px 10px;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    font-size: 13px;
    cursor: pointer;
}
.btn-stop {
    background: #e74c3c;
    color: white;
    padding: 5px 10px;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    font-size: 13px;
    cursor: pointer;
}
.btn-start:hover { background: #219150; }
.btn-stop:hover  { background: #c0392b; }

.status-active   { color: #27ae60; font-weight: bold; }
.status-inactive { color: #e74c3c; font-weight: bold; }
</style>
</head>

<body>
<div class="container">

<h2>Manage Elections</h2>
<hr>

<!-- ================= CREATE FORM ================= -->
<h3>Create New Election</h3>

<form method="POST">
    Title:<br>
    <input type="text" name="title" required><br><br>

    Type:<br>
    <select name="type">
        <option>College</option>
        <option>School</option>
        <option>Club</option>
        <option>Organization</option>
    </select><br><br>

    Start Date:<br>
    <input type="date" name="start" required><br><br>

    End Date:<br>
    <input type="date" name="end" required><br><br>

    Status:<br>
    <select name="status">
        <option>Active</option>
        <option>Inactive</option>
    </select><br><br>

    <button name="create_election">Create Election</button>
</form>

<hr>

<!-- ================= ELECTIONS TABLE ================= -->
<h3>All Elections</h3>

<table border="1" cellpadding="10">
<tr>
    <th>ID</th>
    <th>Title</th>
    <th>Type</th>
    <th>Start Date</th>
    <th>End Date</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($elections)){ ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo htmlspecialchars($row['title']); ?></td>
    <td><?php echo htmlspecialchars($row['type']); ?></td>
    <td><?php echo $row['start_date']; ?></td>
    <td><?php echo $row['end_date']; ?></td>
    <td>
        <span class="<?php echo $row['status']=='Active' ? 'status-active' : 'status-inactive'; ?>">
            <?php echo $row['status']; ?>
        </span>
    </td>
    <td>
        <!-- ✅ Start / Stop Toggle Button -->
        <?php if($row['status'] == 'Active'){ ?>
            <a href="?toggle=<?php echo $row['id']; ?>"
               class="btn-stop"
               onclick="return confirm('Stop this election?')">⏹ Stop</a>
        <?php } else { ?>
            <a href="?toggle=<?php echo $row['id']; ?>"
               class="btn-start"
               onclick="return confirm('Start this election?')">▶ Start</a>
        <?php } ?>
        &nbsp;|&nbsp;
        <a href="?edit=<?php echo $row['id']; ?>">Edit</a>
        &nbsp;|&nbsp;
        <a href="?delete=<?php echo $row['id']; ?>"
           onclick="return confirm('Delete this election?')">Delete</a>
    </td>
</tr>
<?php } ?>
</table>

<hr>

<!-- ================= EDIT FORM ================= -->
<?php
if(isset($_GET['edit'])){
    $edit_id   = (int)$_GET['edit'];
    $edit_q    = mysqli_query($conn, "SELECT * FROM elections WHERE id=$edit_id");
    $edit_data = mysqli_fetch_assoc($edit_q);
    if($edit_data){
?>
<h3>Edit Election</h3>
<form method="POST">
    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">

    Title:<br>
    <input type="text" name="title"
           value="<?php echo htmlspecialchars($edit_data['title']); ?>" required><br><br>

    Type:<br>
    <select name="type">
        <?php foreach(['College','School','Club','Organization'] as $t){ ?>
        <option <?php echo ($edit_data['type']==$t)?'selected':''; ?>><?php echo $t; ?></option>
        <?php } ?>
    </select><br><br>

    Start Date:<br>
    <input type="date" name="start"
           value="<?php echo $edit_data['start_date']; ?>" required><br><br>

    End Date:<br>
    <input type="date" name="end"
           value="<?php echo $edit_data['end_date']; ?>" required><br><br>

    Status:<br>
    <select name="status">
        <option <?php echo ($edit_data['status']=='Active')?'selected':''; ?>>Active</option>
        <option <?php echo ($edit_data['status']=='Inactive')?'selected':''; ?>>Inactive</option>
    </select><br><br>

    <button name="update_election">Update Election</button>
</form>
<?php } } ?>

<br>
<a href="dashboard.php">Back to Dashboard</a>

</div>
</body>
</html>
