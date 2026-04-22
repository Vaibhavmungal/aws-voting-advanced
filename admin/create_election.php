<?php
session_start();
include("../config/database.php");

$page_title = "Create Election";
include("includes/header.php");

$error   = '';
$success = '';

if (isset($_POST['create'])) {
    $title  = trim($_POST['title'] ?? '');
    $type   = $_POST['type']   ?? 'College';
    $start  = $_POST['start']  ?? '';
    $end    = $_POST['end']    ?? '';
    $status = $_POST['status'] ?? 'Active';

    if ($title === '' || $start === '' || $end === '') {
        $error = 'Please fill in all required fields.';
    } elseif ($end < $start) {
        $error = 'End date cannot be before the start date.';
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO elections (title, type, start_date, end_date, status)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssss", $title, $type, $start, $end, $status);
        if ($stmt->execute()) {
            header("Location: manage_elections.php?created=1");
            exit();
        } else {
            $error = 'Database error: ' . htmlspecialchars($stmt->error);
        }
        $stmt->close();
    }
}
?>

<!-- ── Breadcrumb ── -->
<div style="margin-bottom:20px;">
    <a href="manage_elections.php" class="btn btn-outline btn-sm">← Back to Elections</a>
</div>

<h1 class="page-title">🗳️ Create New Election</h1>
<p class="page-subtitle">Fill in the details below to schedule a new election.</p>

<?php if ($error): ?>
<div class="alert alert-danger">⚠️ <?php echo $error; ?></div>
<?php endif; ?>

<div class="card" style="max-width:640px;">
    <div class="card-title">📋 Election Details</div>

    <form method="POST" novalidate>

        <div class="form-group">
            <label for="title">Election Title <span style="color:#ef4444;">*</span></label>
            <input type="text" id="title" name="title"
                   placeholder="e.g. Student Council Election 2026"
                   value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                   required>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="type">Election Type</label>
                <select id="type" name="type">
                    <?php foreach (['College','School','Club','Organization'] as $t): ?>
                    <option value="<?php echo $t; ?>"
                        <?php echo (($_POST['type'] ?? '') === $t) ? 'selected' : ''; ?>>
                        <?php echo $t; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="Active"   <?php echo (($_POST['status'] ?? 'Active') === 'Active')   ? 'selected' : ''; ?>>🟢 Active</option>
                    <option value="Inactive" <?php echo (($_POST['status'] ?? '') === 'Inactive') ? 'selected' : ''; ?>>🔴 Inactive</option>
                </select>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="start">Start Date <span style="color:#ef4444;">*</span></label>
                <input type="date" id="start" name="start"
                       value="<?php echo htmlspecialchars($_POST['start'] ?? ''); ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="end">End Date <span style="color:#ef4444;">*</span></label>
                <input type="date" id="end" name="end"
                       value="<?php echo htmlspecialchars($_POST['end'] ?? ''); ?>"
                       required>
            </div>
        </div>

        <div style="display:flex;gap:12px;margin-top:8px;">
            <button type="submit" name="create" class="btn btn-primary">✅ Create Election</button>
            <a href="manage_elections.php" class="btn btn-outline">Cancel</a>
        </div>

    </form>
</div>

<?php include("includes/footer.php"); ?>
