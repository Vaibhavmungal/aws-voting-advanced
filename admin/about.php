<?php
session_start();
include("../config/database.php");

$page_title = "About System";
include("includes/header.php");
?>

<h1 class="page-title">ℹ️ About VoteSecure</h1>
<p class="page-subtitle">System information and developer credits.</p>

<div class="form-grid">
    <div class="card">
        <div class="card-title">About Online Voting System</div>
        <p style="color:#64748b;line-height:1.6;">
            This Online Voting System is designed to provide a secure,
            fast and transparent digital election platform for college voting.
            Students can vote online while administrators manage elections
            and candidates easily.
        </p>
        
        <h3 style="margin-top: 24px; margin-bottom: 12px; font-size: 1rem; color: var(--text-main);">System Features</h3>
        <ul style="color:#64748b;line-height:1.8;padding-left:20px;">
            <li>Create and manage multiple elections</li>
            <li>Add candidates with profile images</li>
            <li>Secure voting system with password hashing</li>
            <li>Live automatic vote counting</li>
            <li>Winner detection and medal rankings</li>
            <li>Student feedback system</li>
        </ul>
    </div>

    <div class="card">
        <div class="card-title">Developer Information</div>
        <div style="display:flex; flex-direction:column; gap:16px;">
            <div>
                <span style="font-size: .8rem; color:#94a3b8; text-transform:uppercase; font-weight:600;">Developer Name</span>
                <p style="font-size: 1.1rem; font-weight: 700; color: var(--primary);">Vaibhav</p>
            </div>
            
            <div>
                <span style="font-size: .8rem; color:#94a3b8; text-transform:uppercase; font-weight:600;">Project Name</span>
                <p style="font-size: 1rem; font-weight: 600; color: var(--text-main);">VoteSecure Advanced</p>
            </div>
            
            <div>
                <span style="font-size: .8rem; color:#94a3b8; text-transform:uppercase; font-weight:600;">Technology Stack</span>
                <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:6px;">
                    <span class="badge badge-info">PHP 8</span>
                    <span class="badge badge-info">MySQL</span>
                    <span class="badge badge-info">HTML5</span>
                    <span class="badge badge-info">CSS3</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>