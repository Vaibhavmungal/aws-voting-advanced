<?php
session_start();
include("../config/database.php");

$page_title = "Manage Candidates";

/* ── DELETE ── */
if(isset($_GET['delete'])){
    $id   = (int)$_GET['delete'];
    $stmt = $conn->prepare("SELECT image, name FROM candidates WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $img = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if($img && !empty($img['image'])){
        $path = "../uploads/" . $img['image'];
        if(file_exists($path)) unlink($path);
    }

    $stmt = $conn->prepare("DELETE FROM candidates WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    
    // Add log
    $c_name = $img ? $conn->real_escape_string($img['name']) : 'Unknown';
    $conn->query("INSERT INTO logs (action) VALUES ('Admin deleted candidate: $c_name')");
    
    header("Location: manage_candidates.php?msg=deleted");
    exit();
}

/* ── UPDATE ── */
if(isset($_POST['update_candidate'])){
    $id       = (int)$_POST['id'];
    $name     = trim($_POST['name']);
    $position = trim($_POST['position']);
    $stmt = $conn->prepare("UPDATE candidates SET name=?, position=? WHERE id=?");
    $stmt->bind_param("ssi", $name, $position, $id);
    $stmt->execute();
    $stmt->close();
    
    // Add log
    $conn->query("INSERT INTO logs (action) VALUES ('Admin updated candidate: " . $conn->real_escape_string($name) . "')");
    
    header("Location: manage_candidates.php?msg=updated");
    exit();
}

/* ── ADD ── */
if(isset($_POST['add_candidate'])){
    $name        = trim($_POST['name']);
    $position    = trim($_POST['position']);
    $election_id = (int)$_POST['election_id'];
    $image_name  = '';

    if(!empty($_FILES['image']['name'])){
        $ext        = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_name = uniqid('cand_') . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $image_name);
    }

    $stmt = $conn->prepare(
        "INSERT INTO candidates (name, position, election_id, image) VALUES (?,?,?,?)"
    );
    $stmt->bind_param("ssis", $name, $position, $election_id, $image_name);
    $stmt->execute();
    $stmt->close();
    
    // Add log
    $conn->query("INSERT INTO logs (action) VALUES ('Admin added candidate: " . $conn->real_escape_string($name) . "')");
    
    header("Location: manage_candidates.php?msg=added");
    exit();
}

// All candidates with vote counts
$candidates = $conn->query(
    "SELECT c.*, e.title as election_title,
            (SELECT COUNT(*) FROM votes WHERE candidate_id=c.id) as vote_count
     FROM candidates c
     JOIN elections e ON c.election_id=e.id
     ORDER BY c.id DESC"
)->fetch_all(MYSQLI_ASSOC);

$elections = $conn->query("SELECT id, title FROM elections ORDER BY title ASC")->fetch_all(MYSQLI_ASSOC);

// Edit mode
$edit_data = null;
if(isset($_GET['edit'])){
    $edit_id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM candidates WHERE id=?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

include("includes/header.php");
$msg = $_GET['msg'] ?? '';
?>

<h1 class="page-title">👤 Manage Candidates</h1>
<p class="page-subtitle">Add, edit or remove candidates for each election.</p>

<?php if($msg === 'added'):   ?><div class="alert alert-success">✅ Candidate added.</div><?php endif; ?>
<?php if($msg === 'updated'): ?><div class="alert alert-success">✅ Candidate updated.</div><?php endif; ?>
<?php if($msg === 'deleted'): ?><div class="alert alert-danger">🗑️ Candidate deleted.</div><?php endif; ?>

<!-- ADD / EDIT FORM -->
<div class="card">
    <div class="card-title"><?php echo $edit_data ? '✏️ Edit Candidate' : '➕ Add New Candidate'; ?></div>

    <?php if($edit_data): ?>
    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
        <div class="form-grid">
            <div class="form-group">
                <label for="edit_name">Full Name</label>
                <input type="text" id="edit_name" name="name" required
                       value="<?php echo htmlspecialchars($edit_data['name']); ?>">
            </div>
            <div class="form-group">
                <label for="edit_position">Position</label>
                <input type="text" id="edit_position" name="position" required
                       value="<?php echo htmlspecialchars($edit_data['position']); ?>">
            </div>
        </div>
        <button name="update_candidate" class="btn btn-primary">💾 Save Changes</button>
        <a href="manage_candidates.php" class="btn btn-outline" style="margin-left:8px;">Cancel</a>
    </form>
    <?php else: ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Candidate's full name" required>
            </div>
            <div class="form-group">
                <label for="position">Position Running For</label>
                <input type="text" id="position" name="position" placeholder="e.g. President" required>
            </div>
            <div class="form-group">
                <label for="election_id">Election</label>
                <select id="election_id" name="election_id" required>
                    <option value="">Select Election</option>
                    <?php foreach($elections as $e): ?>
                    <option value="<?php echo $e['id']; ?>"><?php echo htmlspecialchars($e['title']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="image">Candidate Photo <small class="text-muted">(optional)</small></label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
        </div>
        <button name="add_candidate" class="btn btn-primary">➕ Add Candidate</button>
    </form>
    <?php endif; ?>
</div>

<!-- CANDIDATES TABLE -->
<div class="card">
    <div class="actions-row">
        <div class="card-title" style="margin:0;border:none;padding:0;">All Candidates</div>
        <div class="search-bar">
            <span class="search-icon">🔍</span>
            <input type="text" id="searchBox" placeholder="Search candidates…">
        </div>
    </div>
    <div class="table-wrap">
        <table class="data-table" id="candidateTable">
            <thead>
                <tr>
                    <th>Photo</th><th>Name</th><th>Position</th>
                    <th>Election</th><th>Votes</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if(empty($candidates)): ?>
                <tr><td colspan="6" style="text-align:center;color:#64748b;padding:24px;">No candidates yet.</td></tr>
            <?php else: ?>
            <?php foreach($candidates as $row): ?>
                <tr>
                    <td>
                        <?php if(!empty($row['image'])): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>"
                                 alt="<?php echo htmlspecialchars($row['name']); ?>"
                                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                            <div style="width:40px;height:40px;border-radius:50%;background:#e0e7ff;display:none;align-items:center;justify-content:center;">👤</div>
                        <?php else: ?>
                            <div style="width:40px;height:40px;border-radius:50%;background:#e0e7ff;display:flex;align-items:center;justify-content:center;">👤</div>
                        <?php endif; ?>
                    </td>
                    <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['position']); ?></td>
                    <td><?php echo htmlspecialchars($row['election_title']); ?></td>
                    <td><span class="badge badge-info"><?php echo $row['vote_count']; ?></span></td>
                    <td>
                        <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-outline btn-xs">✏️ Edit</a>
                        <a href="?delete=<?php echo $row['id']; ?>"
                           class="btn btn-danger btn-xs"
                           onclick="return confirm('Delete this candidate?')">🗑️</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById("searchBox").addEventListener("keyup", function(){
    const val  = this.value.toLowerCase();
    document.querySelectorAll("#candidateTable tbody tr").forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(val) ? "" : "none";
    });
});
</script>

<?php include("includes/footer.php"); ?>
