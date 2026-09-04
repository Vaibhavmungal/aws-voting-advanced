# Contributing to VoteSecure 🗳️

Thank you for your interest in contributing to **VoteSecure**! We welcome all contributions, from bug reports and documentation enhancements to major feature additions.

Please review this guide to get started smoothly.

---

## 🚀 Quick Development Setup

The fastest and most reliable way to run VoteSecure locally is with **Docker**:

```bash
# 1. Fork the repository on GitHub, then clone your fork:
git clone https://github.com/<your-username>/aws-voting-advanced.git
cd aws-voting-advanced

# 2. Start the local stack with Docker Compose:
docker compose up -d
```

Your environment is now live at:
- **VoteSecure App**: [http://localhost:8080](http://localhost:8080)
- **phpMyAdmin (Database GUI)**: [http://localhost:8081](http://localhost:8081)
- **Health Check**: [http://localhost:8080/health.php](http://localhost:8080/health.php)

> **Alternative (XAMPP/WAMP):**
> If you prefer not to use Docker, place the repository inside `htdocs` or `www`, import `database/aws_voting.sql` into MySQL, and copy `.env.example` to `.env`.

---

## 🛠️ Code Conventions & Standards

To ensure code quality and consistency across the project:

### 1. Backend (PHP & MySQL)
- **PHP 8.2+ Compatibility**: Use modern PHP practices.
- **SQL Security (Strict Requirement)**:
  - **Always** use MySQLi prepared statements (`$stmt = $conn->prepare(...)` and `$stmt->bind_param(...)`) for any SQL query handling user input.
  - Never concatenate variables directly into SQL queries.
- **Password Security**: Always use `password_hash($password, PASSWORD_DEFAULT)` and `password_verify()`.
- **Output Escaping**: Use `htmlspecialchars()` when rendering user-submitted text to protect against XSS.

### 2. Frontend (HTML & CSS)
- **Vanilla CSS**: We intentionally avoid heavy frameworks like Bootstrap or Tailwind to keep the app lightweight and customizable.
- **CSS Variables**: Use the existing design tokens in `assets/css/voter.css` and `assets/css/admin.css` (e.g., `--primary-purple: #7c3aed`, `--gold: #f59e0b`).
- **Responsive Layout**: Ensure pages look great on both mobile screens (< 600px) and desktops.

---

## 🧪 Testing Your Changes

Before submitting a Pull Request, please run the following checks:

### 1. PHP Syntax Check
Ensure there are zero syntax errors across the codebase:
```bash
# In Linux/macOS or Docker:
docker exec votesecure_app find . -name "*.php" -exec php -l {} +
```

### 2. Manual Verification
- Test user registration and voter login.
- Test casting a ballot (ensure a voter cannot vote twice in the same election).
- Test admin login and dashboard analytics.
- Test candidate creation and image upload.

---

## 🌿 Git Workflow & Pull Requests

1. **Create a topic branch** from `main`:
   ```bash
   git checkout -b feature/your-feature-name
   # or
   git checkout -b fix/issue-description
   ```

2. **Make atomic, well-described commits**:
   ```bash
   git commit -m "feat: add election export to PDF"
   # or
   git commit -m "fix: resolve voter registration mobile number regex"
   ```

3. **Push to your fork**:
   ```bash
   git push origin feature/your-feature-name
   ```

4. **Open a Pull Request**:
   - Fill out the provided [Pull Request Template](.github/PULL_REQUEST_TEMPLATE.md).
   - Link any related issues (e.g., `Closes #12`).
   - Maintainers will review your PR and provide feedback promptly!

---

## 🐛 Reporting Bugs

If you find a bug:
1. Check existing GitHub Issues to see if it has already been reported.
2. If not, open a new issue using our [Bug Report Template](.github/ISSUE_TEMPLATE/bug_report.md).
3. Include clear steps to reproduce, expected behavior, and relevant environment details (OS, PHP/Docker version).

---

## 💡 Proposing Features

Have an idea for improving VoteSecure? Open a feature request using our [Feature Request Template](.github/ISSUE_TEMPLATE/feature_request.md). We love community suggestions!

---

## 📜 Code of Conduct

Please note that this project is released with a [Contributor Code of Conduct](CODE_OF_CONDUCT.md). By participating in this project, you agree to abide by its terms.
