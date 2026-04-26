# 🗳️ VoteSecure — Advanced Online Voting System

VoteSecure is a modern, secure, and responsive PHP-based online voting platform built for **colleges, NGOs, and small organisations**. It is split into two distinct panels: an **Admin Panel** for election management and a **Voter Panel** for secure ballot casting.

---

## ✨ Features

### 🛡️ Core Security & Architecture
- **Prepared Statements (MySQLi):** 100% immune to SQL Injection. All user inputs are strictly parameterised.
- **Bcrypt Password Hashing:** Uses PHP's native `password_hash()` / `password_verify()`. Legacy plain-text passwords are auto-upgraded to bcrypt on first login.
- **Session Protection:** Strict boundary between Voter and Admin sessions. Redirect guards on every page.
- **Environment Variables:** Database credentials stored securely in `.env` (excluded via `.gitignore`).
- **XSS Protection:** `htmlspecialchars()` used universally when outputting user-generated content.

### 🧑‍🎓 Voter Panel (Theme: Royal Purple + Gold)
- **Registration:** Collects Name, Email, Aadhar/ID Card (12-digit), Mobile (10-digit), and Password. Full duplicate-checking for email and ID card.
- **Login:** Email + Password (fast, universal — works for colleges, NGOs, small elections).
- **Identity Verification:** Aadhar/ID Card and Mobile stored securely for admin-side identity verification.
- **Profile Management:** View registered details (including Aadhar/Mobile), and change password.
- **Voting Logic:** One vote per election enforced at both DB and UI levels.
- **Dynamic UI:** Glassmorphism cards, animated elements, show/hide password toggle.
- **Feedback System:** Voters can submit feedback directly to administrators.
- **Forgot Password:** Submits a contact request to the admin panel.

### 👨‍💻 Admin Panel (Theme: Dark Sidebar + Clean Content)
- **Chart.js Dashboard:** Real-time analytics — voter participation doughnut chart, votes-per-election bar chart.
- **Election Management:** Full CRUD (Create, Edit, Delete, Toggle Active/Inactive) — all in one page (`manage_elections.php`).
- **Candidate Management:** Full CRUD with image uploads.
- **Voter Management:** View all voters with Aadhar & Mobile columns, filter by voted/not voted, live search.
- **Add Voter Manually:** Admin can add voters with optional Aadhar/Mobile. Supports College, Faculty, Staff, NGO Member, General Member types.
- **Excel Export:** 1-click download of comprehensive election results.
- **Audit Logs:** Tracks sensitive admin actions (adding voters, toggling elections, etc.).
- **Reset Requests:** Dedicated panel to handle "Forgot Password" requests from voters.
- **About Page:** System information and developer credits.

---

## 📂 Project Structure

```text
aws-voting-advanced/
├── .env                        # Database credentials (not committed)
├── .gitignore                  # Excludes .env, uploads/*, etc.
├── README.md                   # This documentation
├── index.php                   # Public landing page
│
├── config/
│   └── database.php            # MySQL connection (reads .env, fallback defaults)
│
├── database/
│   └── aws_voting.sql          # Full schema + seed data
│
├── uploads/                    # Candidate profile images (gitignored)
│
├── assets/
│   └── css/
│       ├── admin.css           # Admin design system (dark sidebar theme)
│       └── voter.css           # Voter portal + Landing page styles (merged)
│
├── admin/                      # ── ADMIN PORTAL ──
│   ├── login.php               # Admin login
│   ├── logout.php              # Admin session destroy
│   ├── dashboard.php           # Analytics overview (Chart.js)
│   ├── manage_elections.php    # Election CRUD + toggle status
│   ├── manage_candidates.php   # Candidate CRUD + image upload
│   ├── manage_voters.php       # Voter list (with Aadhar/Mobile), filters, search
│   ├── add_voter.php           # Manual voter registration
│   ├── results.php             # Live vote counting + winner highlights
│   ├── export_results.php      # Excel results download
│   ├── export.php              # Voter list Excel export
│   ├── feedback.php            # Voter feedback inbox
│   ├── reset_requests.php      # Password reset request management
│   ├── logs.php                # Admin audit trail
│   ├── about.php               # System info & developer credits
│   └── includes/
│       ├── header.php          # Admin sidebar + topbar
│       └── footer.php          # Admin footer
│
└── voter/                      # ── VOTER PORTAL ──
    ├── login.php               # Voter login (Email + Password)
    ├── logout.php              # Voter session destroy
    ├── register.php            # Registration (Name/Email/Aadhar/Mobile/Password)
    ├── forgot_password.php     # Password reset request form
    ├── dashboard.php           # Active elections list
    ├── vote.php                # Voting ballot interface
    ├── vote_success.php        # Post-vote confirmation screen
    ├── already_voted.php       # Guard page for completed votes
    ├── profile.php             # Account details + password change
    ├── feedback.php            # Feedback submission
    └── includes/
        ├── header.php          # Voter navbar + auth guard
        └── footer.php          # Voter footer
```

---

## 🚀 Setup Instructions

### 1. Clone the Repository
```bash
git clone https://github.com/Vaibhavmungal/aws-voting-advanced.git
cd aws-voting-advanced
```

### 2. Database Setup
- Create a MySQL database named `aws_voting`.
- Import the schema:
```sql
-- In phpMyAdmin or MySQL CLI:
SOURCE database/aws_voting.sql;
```
- Run the following migration to add identity fields (if upgrading from an older version):
```sql
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `election_card` VARCHAR(20) DEFAULT NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `mobile`        VARCHAR(10) DEFAULT NULL;
```

### 3. Environment Configuration
Create or update `.env` in the project root:
```env
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=aws_voting
APP_NAME=VoteSecure
APP_URL=http://localhost/aws-voting-advanced
```

### 4. Web Server
Place the project folder inside your server's web root:
- **XAMPP:** `C:/xampp/htdocs/aws-voting-advanced/`
- **WAMP:** `C:/wamp64/www/aws-voting-advanced/`

### 5. Access the App
| Portal | URL |
|--------|-----|
| Landing Page | `http://localhost/aws-voting-advanced/` |
| Voter Portal | `http://localhost/aws-voting-advanced/voter/login.php` |
| Admin Panel  | `http://localhost/aws-voting-advanced/admin/login.php` |

---

## 🗄️ Database Schema (Key Tables)

| Table | Purpose |
|-------|---------|
| `users` | Voter accounts — stores name, email, bcrypt password, Aadhar/ID card, mobile, type |
| `admins` | Admin credentials |
| `elections` | Election records (title, type, dates, status) |
| `candidates` | Candidates linked to elections (name, image, election_id) |
| `votes` | Vote transactions (user_id → candidate_id, election_id) |
| `feedback` | Voter feedback submissions |
| `logs` | Admin audit trail |
| `password_reset_requests` | Voter "Forgot Password" submissions |

---

## 🔐 Authentication Flow

### Voter Registration
```
Name  →  Email  →  Aadhar/ID Card (12 digits)  →  Mobile (10 digits)  →  Password
```
- Duplicate email **and** duplicate ID card are both rejected.
- Password hashed with `password_hash(PASSWORD_DEFAULT)` (Bcrypt).

### Voter Login
```
Email  +  Password  →  Dashboard
```
- Supports both new (bcrypt) and legacy (plain-text) passwords. Plain-text passwords are **auto-upgraded** to bcrypt on first login.

### Admin Login
- Username + Password → Admin dashboard.

---

## 🎨 UI / UX Design System

| Layer | Detail |
|-------|--------|
| **Font** | Inter (Google Fonts) |
| **Icons** | Native Unicode emojis — zero external dependency |
| **CSS** | Vanilla CSS with CSS Custom Properties (variables). No Bootstrap/Tailwind |
| **Voter Theme** | Royal Purple (`#7c3aed`) + Gold (`#f59e0b`) on light background |
| **Admin Theme** | Dark sidebar (`#0f172a`) + white content area |
| **CSS Files** | `voter.css` (voter portal + landing page merged) · `admin.css` |

---

## 🔒 Security Checklist

| Practice | Status |
|---------|--------|
| SQL Injection prevention (prepared statements) | ✅ All queries |
| XSS prevention (`htmlspecialchars`) | ✅ All outputs |
| CSRF — form actions tied to session | ✅ |
| Password hashing (Bcrypt) | ✅ |
| Plain-text password auto-upgrade | ✅ |
| Duplicate email + ID card enforcement | ✅ |
| Session isolation (voter vs admin) | ✅ |
| File upload type/MIME validation | ✅ |
| `.env` excluded from version control | ✅ |

---

## 🧪 Test Cases (All Passing ✅)

| # | Scenario | Expected |
|---|---------|---------|
| R1 | Empty registration form | "All fields are required" |
| R2 | Invalid email format | Email validation error |
| R3 | Aadhar < 12 digits | "Must be exactly 12 digits" |
| R4 | Mobile < 10 digits | "Must be exactly 10 digits" |
| R5 | Passwords don't match | Mismatch error |
| R6 | Password < 6 chars | Length error |
| R7 | Valid registration | Success → sign-in link |
| R8 | Duplicate email | "Already registered" |
| R9 | Duplicate Aadhar | "Already registered" |
| L1 | Empty login | "Fields required" |
| L2 | Unknown email | "No account found" |
| L3 | Wrong password | "Incorrect password" |
| L4 | Valid credentials | Redirect to dashboard |

---

## 👨‍💻 Developer

**Vaibhav Mungal**
Full-stack developer | PHP · MySQL · Vanilla CSS

> *Built with ❤️, PHP, MySQL, and a lot of CSS variables. Suitable for college elections, NGO voting, and small organisational polls.*
