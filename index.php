<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="VoteSecure – Online Voting System. Secure, fair and transparent elections.">
    <title>VoteSecure – Online Voting System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/landing.css">
</head>
<body>

<!-- ══════════ HERO SECTION ══════════ -->
<section class="hero">

    <div class="hero-badge">🔐 Secure &amp; Transparent Elections</div>

    <div class="hero-icon">🗳️</div>

    <h1>VoteSecure</h1>
    <p class="hero-sub">
        A modern, fair and tamper-proof online voting platform for colleges,
        schools and organisations. Cast your vote with confidence.
    </p>

    <!-- CTA Buttons -->
    <div class="cta-group">
        <a href="voter/login.php" class="btn btn-voter">🧑‍🎓 Voter Login</a>
        <a href="admin/login.php" class="btn btn-admin">🔐 Admin Panel</a>
    </div>

    <!-- Feature Cards -->
    <div class="features">
        <div class="feature-card">
            <div class="fi">🔒</div>
            <h3>Secure Voting</h3>
            <p>Each voter can only vote once. All data is protected with hashed credentials.</p>
        </div>
        <div class="feature-card">
            <div class="fi">📊</div>
            <h3>Live Results</h3>
            <p>Real-time vote counts with progress bars and winner detection.</p>
        </div>
        <div class="feature-card">
            <div class="fi">🏫</div>
            <h3>Multi-Election</h3>
            <p>Run multiple elections simultaneously for different groups or events.</p>
        </div>
        <div class="feature-card">
            <div class="fi">📱</div>
            <h3>Responsive</h3>
            <p>Works seamlessly on desktop, tablet and mobile devices.</p>
        </div>
    </div>

</section>

<!-- ══════════ DEVELOPER SECTION ══════════ -->
<section class="dev-section">
    <div class="dev-card">
        <div class="dev-avatar">👨‍💻</div>
        <div class="dev-info">
            <span class="dev-label">Built &amp; Designed by</span>
            <h2 class="dev-name">Vaibhav</h2>
            <p class="dev-desc">
                Full-stack developer passionate about building secure, modern web applications.
                VoteSecure was crafted with ❤️ using PHP, MySQL &amp; vanilla CSS.
            </p>
            <div class="dev-tags">
                <span class="tag">PHP</span>
                <span class="tag">MySQL</span>
                <span class="tag">HTML &amp; CSS</span>
                <span class="tag">Security</span>
            </div>
        </div>
    </div>
</section>

<!-- ══════════ FOOTER ══════════ -->
<footer class="landing-footer">
    <span>© <?php echo date('Y'); ?> VoteSecure</span>
    <span class="footer-dot">·</span>
    <span>Designed &amp; Developed by <strong style="color:#a78bfa;">Vaibhav</strong></span>
</footer>

</body>
</html>
