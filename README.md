# 🗳️ VoteSecure — Advanced Online Voting System

VoteSecure is a modern, secure, and responsive PHP-based online voting platform built for **colleges, NGOs, and small organisations**. It is split into two distinct panels: an **Admin Panel** for election management and a **Voter Panel** for secure ballot casting.

> 🌐 **Live Demo:** [http://13.206.147.173/](http://13.206.147.173/)

---

## 📂 Project Structure

```
aws-voting-advanced/
├── .env                        # Database credentials (not committed)
├── .env.example                # Sample environment config
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

## ✨ Features
                                           
### 🛡️ Core Security & Architecture
- **Prepared Statements (MySQLi):** 100% immune to SQL Injection. All user inputs are strictly parameterised.
- **Bcrypt Password Hashing:** Uses PHP's native `password_hash()` / `password_verify()`. Legacy plain-text passwords are auto-upgraded to bcrypt on first login.
- **Session Protection:** Strict boundary between Voter and Admin sessions. Redirect guards on every page.
- **Environment Variables:** Database credentials stored securely in `.env` (excluded via `.gitignore`).
- **XSS Protection:** `htmlspecialchars()` used universally when outputting user-generated content.
- **Mobile Responsive Design:** 100% optimized for smartphone and tablet viewports with responsive column wrapping, collapsible menu systems, and fluid grids.

### 🧑‍🎓 Voter Panel (Theme: Royal Purple + Gold)
- **Registration:** Collects Name, Email, Aadhar/ID Card (12-digit), Mobile (10-digit), and Password. Full duplicate-checking for email and ID card.
- **Open Email Registration:** Configurable via `ALLOWED_EMAIL_DOMAIN` in `.env` — set to `all` to accept Gmail/any email, or restrict to a domain (e.g., `@college.ac.in`).
- **Login:** Email + Password (fast, universal — works for colleges, NGOs, small elections).
- **Identity Verification:** Aadhar/ID Card and Mobile stored securely for admin-side identity verification.
- **Profile Management:** View registered details (including Aadhar/Mobile), and change password.
- **Voting Logic:** One vote per election enforced at both DB and UI levels.
- **Dynamic UI:** Glassmorphism cards, animated elements, show/hide password toggle.
- **Feedback System:** Voters can submit feedback directly to administrators.
- **Forgot Password:** Submits a contact request to the admin panel.

### 👨‍💻 Admin Panel (Theme: Dark Sidebar + Clean Content)
- **Chart.js Dashboard:** Real-time analytics — voter participation doughnut chart + votes-per-election bar chart displayed **side by side** in one row.
- **Stat Cards:** 6 key metrics (Total Elections, Active Elections, Registered Voters, Voters Participated, Candidates, Vote Transactions) displayed in a **single compact row**.
- **Election Management:** Full CRUD (Create, Edit, Delete, Toggle Active/Inactive) — all in one page (`manage_elections.php`).
- **Candidate Management:** Full CRUD with image uploads.
- **Voter Management:** View all voters with Aadhar & Mobile columns, filter by voted/not voted, live search.
- **Add Voter Manually:** Admin can add voters with optional Aadhar/Mobile. Supports College, Faculty, Staff, NGO Member, General Member types.
- **Excel Export:** 1-click download of comprehensive election results.
- **Audit Logs:** Tracks sensitive admin actions (adding voters, toggling elections, etc.).
- **Reset Requests:** Dedicated panel to handle "Forgot Password" requests from voters.
- **About Page:** System information and developer credits.

---

## 🚀 Local Setup (XAMPP/WAMP)

### 1. Clone the Repository
```bash
git clone https://github.com/Vaibhavmungal/aws-voting-advanced.git
cd aws-voting-advanced
```

### 2. Database Setup
- Create a MySQL database named `aws_voting`.
- Import the schema:
```sql
SOURCE database/aws_voting.sql;
```

### 3. Environment Configuration
Create `.env` in the project root (copy from `.env.example`):
```env
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=aws_voting
APP_NAME=VoteSecure
ALLOWED_EMAIL_DOMAIN=all
APP_URL=http://localhost/aws-voting-advanced
```

> **`ALLOWED_EMAIL_DOMAIN`** options:
> - `all` → Accept any email (Gmail, Yahoo, etc.)
> - `@college.ac.in` → Restrict to a specific college domain

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

## ☁️ AWS EC2 Deployment

### Prerequisites
- Ubuntu EC2 instance with Apache2, PHP 8+, MySQL running
- Project files located at `/var/www/html/`

### First-Time Deployment
```bash
# Go to web root
cd /var/www/html

# Initialize git and connect to repo
git init
git config --global --add safe.directory /var/www/html
git remote add origin https://github.com/Vaibhavmungal/aws-voting-advanced.git

# Pull latest code
git fetch origin main
git reset --hard origin/main

# Create .env (not stored in git)
nano /var/www/html/.env
```

Add this to `.env` on the server:
```env
DB_HOST=localhost
DB_USER=root
DB_PASS=your_mysql_password
DB_NAME=aws_voting
APP_NAME=VoteSecure
ALLOWED_EMAIL_DOMAIN=all
APP_URL=http://your-ec2-ip
```

### Fix File Permissions
```bash
chown -R www-data:www-data /var/www/html/
chmod -R 755 /var/www/html/
chmod -R 777 /var/www/html/uploads/
```

### Updating the Server (after pushing changes)
```bash
cd /var/www/html
git stash          # save any local-only changes
git pull origin main
```

---

## 🗄️ Database Schema (Key Tables)

| Table | Purpose |
|-------|---------
| `users` | Voter accounts — stores name, email, bcrypt password, Aadhar/ID card, mobile, type |
| `admins` | Admin credentials |
| `elections` | Election records (title, type, dates, status) |
| `candidates` | Candidates linked to elections (name, image, election_id, manifesto) |
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
| **Responsiveness** | Fully optimized for mobile & tablet — collapsible icon-only sidebar, stacked auth forms, fluid stat card row, side-by-side charts on desktop |

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

## 📱 Mobile Responsiveness

| Page | Fix Applied |
|------|------------|
| `voter/register.php` | Replaced `overflow: hidden` → `overflow-x: hidden` + `-webkit-overflow-scrolling: touch` for smooth scroll |
| `voter/login.php` | Same scroll fix applied |
| `voter/forgot_password.php` | Same scroll fix applied |
| Admin sidebar | Fixed selector so icons remain visible (only labels hide on mobile) |
| Admin stat cards | Reduced `minmax` to `130px` — all 6 cards fit in one row on desktop |
| Admin charts | Both charts remain side-by-side on desktop, stack only below 600px |
| Voter navbar | Compact padding on small screens |
| Ballot candidate cards | Grid layout for very small viewports (< 480px) |

---

## 🧪 Test Cases (All Passing ✅)

| # | Scenario | Expected |
|---|---------|---------
| R1 | Empty registration form | "All fields are required" |
| R2 | Invalid email format | Email validation error |
| R3 | Aadhar < 12 digits | "Must be exactly 12 digits" |
| R4 | Mobile < 10 digits | "Must be exactly 10 digits" |
| R5 | Passwords don't match | Mismatch error |
| R6 | Password < 6 chars | Length error |
| R7 | Valid registration (any email) | Success → sign-in link |
| R8 | Duplicate email | "Already registered" |
| R9 | Duplicate Aadhar | "Already registered" |
| L1 | Empty login | "Fields required" |
| L2 | Unknown email | "No account found" |
| L3 | Wrong password | "Incorrect password" |
| L4 | Valid credentials | Redirect to dashboard |

---

## 👨‍💻 Developer

**Vaibhav Mungal**

> *Built with ❤️, PHP, MySQL, and a lot of CSS variables. Deployed on AWS EC2. Suitable for college elections, NGO voting, and small organisational polls.*
