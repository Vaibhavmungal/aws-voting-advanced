<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="VoteSecure – Online Voting System. Secure, fair and transparent elections.">
    <title>VoteSecure – Online Voting System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Inter', Arial, sans-serif;
        background: #060012;
        color: #e2e8f0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    a { text-decoration: none; color: inherit; }

    /* ── HERO ── */
    .hero {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 80px 24px 60px;
        min-height: 100vh;
        background: url('assets/images/hero-bg.png') center/cover no-repeat #060012;
        position: relative;
        overflow: hidden;
    }
    .hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(6, 0, 18, 0.45);
        z-index: 0;
    }
    .hero > * {
        position: relative;
        z-index: 1;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(124,58,237,.15);
        border: 1px solid rgba(124,58,237,.45);
        color: #c4b5fd;
        padding: 7px 20px;
        border-radius: 50px;
        font-size: .82rem;
        font-weight: 700;
        margin-bottom: 28px;
        letter-spacing: .04em;
    }

    .hero-icon {
        font-size: 5.5rem;
        margin-bottom: 20px;
        animation: float 3s ease-in-out infinite;
        filter: drop-shadow(0 0 28px rgba(124,58,237,.55));
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50%      { transform: translateY(-14px); }
    }

    .hero h1 {
        font-size: clamp(2.6rem, 7vw, 4.4rem);
        font-weight: 900;
        line-height: 1.08;
        letter-spacing: -.04em;
        background: linear-gradient(135deg, #fff 0%, #c4b5fd 50%, #f59e0b 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 18px;
    }

    .hero-sub {
        font-size: 1.1rem;
        color: #9d8ec8;
        max-width: 520px;
        line-height: 1.7;
        margin-bottom: 44px;
    }

    /* ── CTA ── */
    .cta-group {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        justify-content: center;
    }
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 32px;
        border-radius: 14px;
        font-size: 1rem;
        font-weight: 700;
        transition: transform .22s, box-shadow .22s;
    }
    .btn:hover { transform: translateY(-3px); }

    .btn-voter {
        background: linear-gradient(135deg, #7c3aed, #5b21b6);
        color: #fff;
        box-shadow: 0 8px 30px rgba(124,58,237,.5);
    }
    .btn-voter:hover { box-shadow: 0 14px 42px rgba(124,58,237,.7); }

    .btn-admin {
        background: linear-gradient(135deg, rgba(245,158,11,.18), rgba(245,158,11,.08));
        color: #fcd34d;
        border: 1px solid rgba(245,158,11,.4);
    }
    .btn-admin:hover {
        background: linear-gradient(135deg, rgba(245,158,11,.28), rgba(245,158,11,.15));
        border-color: rgba(245,158,11,.7);
        box-shadow: 0 8px 28px rgba(245,158,11,.25);
    }

    /* ── FEATURES ── */
    .features {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        max-width: 900px;
        width: 100%;
        margin-top: 64px;
    }

    .feature-card {
        background: rgba(124,58,237,.07);
        border: 1px solid rgba(124,58,237,.2);
        border-radius: 16px;
        padding: 24px 20px;
        text-align: left;
        transition: background .22s, border-color .22s, transform .22s;
    }
    .feature-card:hover {
        background: rgba(124,58,237,.14);
        border-color: rgba(124,58,237,.4);
        transform: translateY(-3px);
    }
    .feature-card .fi { font-size: 1.9rem; margin-bottom: 12px; }
    .feature-card h3  { font-size: .95rem; font-weight: 700; color: #ddd6fe; margin-bottom: 6px; }
    .feature-card p   { font-size: .82rem; color: #6b7280; line-height: 1.55; }

    /* ── DEVELOPER ── */
    .dev-section {
        background: #080112;
        padding: 64px 24px;
        display: flex;
        justify-content: center;
        border-top: 1px solid rgba(124,58,237,.15);
    }
    .dev-card {
        display: flex;
        align-items: center;
        gap: 36px;
        background: rgba(124,58,237,.07);
        border: 1px solid rgba(124,58,237,.22);
        border-radius: 22px;
        padding: 40px 44px;
        max-width: 720px;
        width: 100%;
        transition: background .22s, border-color .22s;
    }
    .dev-card:hover { background: rgba(124,58,237,.12); border-color: rgba(124,58,237,.38); }

    .dev-avatar {
        font-size: 4.5rem;
        flex-shrink: 0;
        background: radial-gradient(circle, rgba(124,58,237,.35), rgba(124,58,237,.1));
        border: 2px solid rgba(124,58,237,.5);
        width: 108px; height: 108px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: float 3s ease-in-out infinite;
        box-shadow: 0 0 30px rgba(124,58,237,.3);
    }
    .dev-label { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .12em; color: #a78bfa; }
    .dev-name {
        font-size: 2.1rem;
        font-weight: 800;
        background: linear-gradient(135deg, #ddd6fe, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin: 6px 0 12px;
        letter-spacing: -.03em;
    }
    .dev-desc { font-size: .88rem; color: #6b7280; line-height: 1.7; margin-bottom: 18px; }
    .dev-tags { display: flex; flex-wrap: wrap; gap: 8px; }
    .tag {
        background: rgba(124,58,237,.15);
        border: 1px solid rgba(124,58,237,.35);
        color: #c4b5fd;
        padding: 4px 13px;
        border-radius: 50px;
        font-size: .75rem;
        font-weight: 700;
    }

    /* ── FOOTER ── */
    .landing-footer {
        text-align: center;
        padding: 20px;
        font-size: .82rem;
        color: #3b2a6e;
        border-top: 1px solid rgba(124,58,237,.1);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background: #080112;
    }
    .footer-dot { color: #2e1065; }

    /* ── RESPONSIVE ── */
    @media (max-width: 768px) {
        .features { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 600px) {
        .features { grid-template-columns: 1fr; }
        .dev-card { flex-direction: column; text-align: center; padding: 30px 22px; }
        .dev-tags { justify-content: center; }
        .dev-avatar { margin-bottom: 8px; }
    }
    </style>
</head>
<body>

<!-- ══════════ HERO ══════════ -->
<section class="hero">
    <div class="hero-badge">🔐 Secure &amp; Transparent Elections</div>
    <div class="hero-icon">🗳️</div>
    <h1>VoteSecure</h1>
    <p class="hero-sub">
        A modern, fair and tamper-proof online voting platform for colleges,
        schools and organisations. Cast your vote with confidence.
    </p>

    <div class="cta-group">
        <a href="voter/login.php" class="btn btn-voter">🧑‍🎓 Voter Login</a>
        <a href="admin/login.php" class="btn btn-admin">🔐 Admin Panel</a>
    </div>

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

<!-- ══════════ DEVELOPER ══════════ -->
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
