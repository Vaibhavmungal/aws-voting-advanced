<?php
session_start();
include("../config/database.php");

$page_title = "Election Results";
include("includes/header.php");

$elections = $conn->query("SELECT * FROM elections ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
?>

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:6px;">
    <div>
        <h1 class="page-title" style="margin-bottom:0;">🏆 Election Results</h1>
        <p class="page-subtitle" style="margin-bottom:0;">Live vote counts and winners for all elections.</p>
    </div>
    <a href="export_results.php" class="btn btn-success" style="gap:8px;" target="_blank">
        📊 Download Full Report (Excel)
    </a>
</div>
<hr class="divider">

<?php if (empty($elections)): ?>
    <div class="card" style="text-align:center;color:#64748b;padding:48px;">
        <div style="font-size:3rem;margin-bottom:12px;">📭</div>
        <p>No elections found.</p>
    </div>
<?php endif; ?>

<?php foreach ($elections as $election):
    $eid = (int)$election['id'];

    // Get candidates with vote counts for this election
    $cands = $conn->query(
        "SELECT c.id, c.name, c.position, c.image, COUNT(v.id) AS votes
         FROM candidates c
         LEFT JOIN votes v ON c.id = v.candidate_id
         WHERE c.election_id = $eid
         GROUP BY c.id
         ORDER BY votes DESC"
    )->fetch_all(MYSQLI_ASSOC);

    $total_votes = array_sum(array_column($cands, 'votes'));
    $max_votes   = !empty($cands) ? (int)$cands[0]['votes'] : 0;
    $winner      = ($max_votes > 0 && !empty($cands)) ? $cands[0] : null;
?>

<div class="card">
    <!-- ── Election Header ── -->
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:16px;flex-wrap:wrap;">
        <div>
            <h2 style="font-size:1.15rem;font-weight:700;margin:0 0 4px;">
                <?php echo htmlspecialchars($election['title']); ?>
            </h2>
            <span style="font-size:.82rem;color:#6b7280;">
                <?php echo htmlspecialchars($election['type']); ?> ·
                Ends <?php echo date('d M Y', strtotime($election['end_date'])); ?> ·
                <strong><?php echo $total_votes; ?></strong> votes cast
            </span>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <span class="badge <?php echo $election['status']==='Active' ? 'badge-success' : 'badge-danger'; ?>">
                <?php echo $election['status']; ?>
            </span>
            <a href="export_results.php?election=<?php echo $eid; ?>"
               class="btn btn-outline btn-sm" target="_blank"
               title="Download Excel report for this election only">
                📥 Download Report
            </a>
        </div>
    </div>

    <!-- ── Winner Box ── -->
    <?php if ($winner): ?>
    <div style="
        background: linear-gradient(135deg, #fef3c7, #fffbeb);
        border: 2px solid #f59e0b;
        border-radius: 14px;
        padding: 18px 22px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 18px;
    ">
        <div style="font-size:3rem;line-height:1;flex-shrink:0;">🏆</div>
        <div>
            <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#92400e;margin-bottom:3px;">
                Election Winner
            </div>
            <div style="font-size:1.25rem;font-weight:800;color:#1e1b4b;">
                <?php echo htmlspecialchars($winner['name']); ?>
            </div>
            <div style="font-size:.85rem;color:#6b7280;margin-top:2px;">
                <?php echo htmlspecialchars($winner['position']); ?> ·
                <strong style="color:#059669;"><?php echo $winner['votes']; ?> votes</strong>
                (<?php echo ($total_votes > 0) ? round(($winner['votes']/$total_votes)*100,1) : 0; ?>%)
            </div>
        </div>
        <?php if (!empty($winner['image'])): ?>
        <img src="../uploads/<?php echo htmlspecialchars($winner['image']); ?>"
             alt="" style="width:58px;height:58px;border-radius:50%;object-fit:cover;border:3px solid #f59e0b;margin-left:auto;"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
        <div style="width:58px;height:58px;border-radius:50%;background:#fde68a;display:none;align-items:center;justify-content:center;font-size:1.6rem;margin-left:auto;border:3px solid #f59e0b;">👤</div>
        <?php else: ?>
        <div style="width:58px;height:58px;border-radius:50%;background:#fde68a;display:flex;align-items:center;justify-content:center;font-size:1.6rem;margin-left:auto;border:3px solid #f59e0b;">👤</div>
        <?php endif; ?>
    </div>
    <?php elseif (empty($cands)): ?>
        <p style="color:#6b7280;font-size:.9rem;margin-bottom:16px;">No candidates in this election.</p>
    <?php else: ?>
        <div style="background:#f0fdf4;border:1px solid #a7f3d0;border-radius:10px;padding:12px 18px;margin-bottom:16px;font-size:.88rem;color:#065f46;">
            📊 No votes cast yet — winner will appear here once voting begins.
        </div>
    <?php endif; ?>

    <!-- ── Candidates Table ── -->
    <?php if (!empty($cands)): ?>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Candidate</th>
                    <th>Position</th>
                    <th>Votes</th>
                    <th>Vote Share</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($cands as $i => $row):
                $pct    = ($total_votes > 0) ? round(($row['votes'] / $total_votes) * 100, 1) : 0;
                $is_win = ($max_votes > 0 && $row['votes'] == $max_votes);
            ?>
            <tr <?php echo $is_win ? 'class="winner-row"' : ''; ?>>
                <td>
                    <?php
                    if ($is_win)   echo '🥇';
                    elseif ($i===1) echo '🥈';
                    elseif ($i===2) echo '🥉';
                    else           echo '#'.($i+1);
                    ?>
                </td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <?php if (!empty($row['image'])): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>"
                                 alt="" style="width:36px;height:36px;border-radius:50%;object-fit:cover;"
                                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                            <div style="width:36px;height:36px;border-radius:50%;background:#ede9fe;display:none;align-items:center;justify-content:center;font-size:.9rem;">👤</div>
                        <?php else: ?>
                            <div style="width:36px;height:36px;border-radius:50%;background:#ede9fe;display:flex;align-items:center;justify-content:center;font-size:.9rem;">👤</div>
                        <?php endif; ?>
                        <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                        <?php if ($is_win): ?><span class="winner-label">Winner</span><?php endif; ?>
                    </div>
                </td>
                <td><?php echo htmlspecialchars($row['position']); ?></td>
                <td><strong><?php echo $row['votes']; ?></strong></td>
                <td style="min-width:140px;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div class="progress-bar-wrap" style="flex:1;">
                            <div class="progress-bar-fill"
                                 style="width:<?php echo $pct; ?>%;<?php echo $is_win?'background:var(--success)':''; ?>">
                            </div>
                        </div>
                        <span style="font-size:.78rem;color:#6b7280;width:36px;"><?php echo $pct; ?>%</span>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php endforeach; ?>

<?php include("includes/footer.php"); ?>