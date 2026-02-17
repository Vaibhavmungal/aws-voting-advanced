<?php
include("../config/database.php");

/* ================= DELETE ELECTION ================= */
if(isset($_GET['delete'])) {

    $id = $_GET['delete'];

    // Delete related candidates first
    mysqli_query($conn, "DELETE FROM candidates WHERE election_id=$id");

    // Delete election
    mysqli_query($conn, "DELETE FROM elections WHERE id=$id");

    header("Location: manage_elections.php");
    exit();
}

/* ================= FETCH ELECTIONS ================= */
$result = mysqli_query($conn, "SELECT * FROM elections");
?>

<h2>All Elections</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>Title</th>
        <th>Type</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<tr>
    <td><?php echo $row['title']; ?></td>
    <td><?php echo $row['type']; ?></td>
    <td><?php echo $row['status']; ?></td>

    <td>

        <!-- Activate / Stop -->
        <?php if($row['status'] == 'Active') { ?>
            <a href="toggle_election.php?id=<?php echo $row['id']; ?>&status=Inactive">
                <button>Stop</button>
            </a>
        <?php } else { ?>
            <a href="toggle_election.php?id=<?php echo $row['id']; ?>&status=Active">
                <button>Activate</button>
            </a>
        <?php } ?>

        |

        <!-- Delete -->
        <a href="manage_elections.php?delete=<?php echo $row['id']; ?>"
           onclick="return confirm('Are you sure you want to delete this election?')">
           <button style="background:red;color:white;">Delete</button>
        </a>

    </td>
</tr>

<?php } ?>

</table>
