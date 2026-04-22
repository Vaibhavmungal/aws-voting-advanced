<?php
session_start();
include("../config/database.php");

$page_title = "Manage Elections";

/* ── CREATE ── */
if(isset($_POST['create_election'])){
    $stmt = $conn->prepare(
        "INSERT INTO elections (title, type, start_date, end_date, status) VALUES (?,?,?,?,?)"
    );
    $title  = trim($_POST['title']);
    $type   = $_POST['type'];
    $start  = $_POST['start'];
    $end    = $_POST['end'];
    $status = $_POST['status'];
    $stmt->bind_param("sssss", $title, $type, $start, $end, $status);
    $stmt->execute();
    $stmt->close();
    
    // Add log
    $conn->query("INSERT INTO logs (action) VALUES ('Admin created election: " . $conn->real_escape_string($title) . "')");
    
    header("Location: manage_elections.php?msg=created");
    exit();
}

/* ── UPDATE ── */
if(isset($_POST['update_election'])){
    $stmt = $conn->prepare(
        "UPDATE elections SET title=?, type=?, start_date=?, end_date=?, status=? WHERE id=?"
    );
    $id     = (int)$_POST['id'];
    $title  = trim($_POST['title']);
    $type   = $_POST['type'];
    $start  = $_POST['start'];
    $end    = $_POST['end'];
    $status = $_POST['status'];
    $stmt->bind_param("sssssi", $title, $type, $start, $end, $status, $id);
    $stmt->execute();
    $stmt->close();
    
    // Add log
    $conn->query("INSERT INTO logs (action) VALUES ('Admin updated election ID: $id ($title)')");
    
    header("Location: manage_elections.php?msg=updated");
    exit();
}

/* ── DELETE ── */
if(isset($_GET['delete'])){
    $id   = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM elections WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    
    // Add log
    $conn->query("INSERT INTO logs (action) VALUES ('Admin deleted election ID: $id')");
    
    header("Location: manage_elections.php?msg=deleted");
    exit();
}

/* ── TOGGLE STATUS ── */
if(isset($_GET['toggle'])){
    $id   = (int)$_GET['toggle'];
    $stmt = $conn->prepare("SELECT status FROM elections WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $cur = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $new_status = ($cur['status'] === 'Active') ? 'Inactive' : 'Active';
    $stmt = $conn->prepare("UPDATE elections SET status=? WHERE id=?");
    $stmt->bind_param("si", $new_status, $id);
    $stmt->execute();
    $stmt->close();
    
    // Add log
    $conn->query("INSERT INTO logs (action) VALUES ('Admin toggled election ID $id to $new_status')");
    
    header("Location: manage_elections.php");
    exit();
}

$elections = $conn->query("SELECT * FROM elections ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);

// Edit mode
$edit_data = null;
if(isset($_GET['edit'])){
    $edit_id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM elections WHERE id=?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

include("includes/header.php");
$msg = $_GET['msg'] ?? '';
?>

<h1 class="page-title">🗳️ Manage Elections</h1>
<p class="page-subtitle">Create, edit and control elections.</p>

<?php if($msg === 'created'): ?><div class="alert alert-success">✅ Election created successfully.</div><?php endif; ?>
<?php if($msg === 'updated'): ?><div class="alert alert-success">✅ Election updated.</div><?php endif; ?>
<?php if($msg === 'deleted'): ?><div class="alert alert-danger">🗑️ Election deleted.</div><?php endif; ?>

<!-- CREATE / EDIT FORM -->
<div class="card">
    <div class="card-title"><?php echo $edit_data ? '✏️ Edit Election' : '➕ Create New Election'; ?></div>
    <form method="POST">
        <?php if($edit_data): ?>
            <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
        <?php endif; ?>

        <div class="form-grid">
            <div class="form-group">
                <label for="title">Election Title</label>
                <input type="text" id="title" name="title" placeholder="e.g. Student Council 2025" required
                       value="<?php echo htmlspecialchars($edit_data['title'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="type">Type</label>
                <select id="type" name="type">
                    <?php foreach(['College','School','Club','Organization'] as $t): ?>
                    <option <?php echo (($edit_data['type'] ?? '') === $t) ? 'selected' : ''; ?>><?php echo $t; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="start">Start Date</label>
                <input type="date" id="start" name="start" required
                       value="<?php echo $edit_data['start_date'] ?? ''; ?>">
            </div>
            <div class="form-group">
                <label for="end">End Date</label>
                <input type="date" id="end" name="end" required
                       value="<?php echo $edit_data['end_date'] ?? ''; ?>">
            </div>
        </div>

        <div class="form-group" style="max-width:200px;">
            <label for="status">Status</label>
            <select id="status" name="status">
                <option <?php echo (($edit_data['status'] ?? '') === 'Active')   ? 'selected' : ''; ?>>Active</option>
                <option <?php echo (($edit_data['status'] ?? '') === 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>

        <?php if($edit_data): ?>
            <button name="update_election" class="btn btn-primary">💾 Update Election</button>
            <a href="manage_elections.php" class="btn btn-outline" style="margin-left:8px;">Cancel</a>
        <?php else: ?>
            <button name="create_election" class="btn btn-primary">➕ Create Election</button>
        <?php endif; ?>
    </form>
</div>

<!-- ELECTIONS TABLE -->
<div class="card">
    <div class="card-title">All Elections</div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th><th>Title</th><th>Type</th>
                    <th>Start</th><th>End</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if(empty($elections)): ?>
                <tr><td colspan="7" style="text-align:center;color:#64748b;padding:24px;">No elections yet.</td></tr>
            <?php else: ?>
            <?php foreach($elections as $row): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['type']); ?></td>
                    <td><?php echo date('d M Y', strtotime($row['start_date'])); ?></td>
                    <td><?php echo date('d M Y', strtotime($row['end_date'])); ?></td>
                    <td>
                        <span class="badge <?php echo $row['status']==='Active' ? 'badge-success' : 'badge-danger'; ?>">
                            <?php echo $row['status']; ?>
                        </span>
                    </td>
                    <td style="white-space:nowrap;">
                        <?php if($row['status'] === 'Active'): ?>
                            <a href="?toggle=<?php echo $row['id']; ?>"
                               class="btn btn-warning btn-xs"
                               onclick="return confirm('Stop this election?')">⏹ Stop</a>
                        <?php else: ?>
                            <a href="?toggle=<?php echo $row['id']; ?>"
                               class="btn btn-success btn-xs"
                               onclick="return confirm('Start this election?')">▶ Start</a>
                        <?php endif; ?>
                        <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-outline btn-xs">✏️ Edit</a>
                        <a href="?delete=<?php echo $row['id']; ?>"
                           class="btn btn-danger btn-xs"
                           onclick="return confirm('Delete this election and all its votes?')">🗑️</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("includes/footer.php"); ?>
