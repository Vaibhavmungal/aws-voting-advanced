# 🗳️ VoteSecure — Advanced Online Voting System

VandoteSecure is a modern, secure,  responsive PHP-based online voting platform. It is split into two distinct panels: an **Admin Panel** for election management and a **Voter Panel** for secure ballot casting.

---

## ✨ Features

### 🛡️ Core Security & Architecture
- **Prepared Statements (PDO/MySQLi):** 100% immune to SQL Injection. All user inputs are strictly parameterized.
- **Secure Password Hashing:** Uses PHP's native `password_hash()` (Bcrypt).
- **Session Protection:** Strict boundary between Voter and Admin sessions. Redirect guards on every page.
- **Environment Variables:** Database credentials stored securely in `.env` (excluded via `.gitignore`).

### 🧑‍🎓 Voter Panel (Theme: Royal Purple + Gold)
- **Authentication:** Registration, Login, and a "Forgot Password" workflow.
- **Profile Management:** Voters can update their details and change their password.
- **Voting Logic:** Voters can only vote **once** per election. The system enforces this at the database and UI levels.
- **Dynamic UI:** Glassmorphism headers, modern cards, and radio-button selections with candidate avatars.
- **Feedback System:** Voters can submit secure feedback directly to the administrators.

### 👨‍💻 Admin Panel (Theme: Dark Sidebar + Clean Content)
- **Chart.js Dashboard:** Real-time analytics (Voter participation doughnut chart, Votes-per-election bar chart).
- **Election Management:** Full CRUD (Create, Read, Update, Delete) for Elections.
- **Candidate Management:** Full CRUD with image uploads.
- **Voter Management:** View registered voters, track who has voted, and export the list.
- **Excel Export:** 1-click download of comprehensive election results (`export_results.php`).
- **Audit Logs:** Tracks sensitive administrative actions (e.g., when a voter is manually added or an election is toggled).
- **Reset Requests:** Dedicated panel to handle "Forgot Password" requests from voters.

---

## 📂 Project Structure

```text
aws-voting-advanced/
├── .env                        # Database configuration variables
├── .gitignore                  # Git exclusions (uploads, .env)
├── README.md                   # This documentation file
├── index.php                   # Public landing page
├── config/
│   └── database.php            # MySQL connection logic (reads .env)
├── database/
│   └── aws_voting.sql          # Complete database schema and initial data
├── uploads/                    # Candidate profile images
├── assets/
│   └── css/
│       ├── admin.css           # Admin design system
│       └── voter.css           # Voter design system
│
├── admin/                      # ADMINISTRATIVE PORTAL
│   ├── dashboard.php           # Chart.js analytics overview
│   ├── login.php / logout.php  # Auth flow
│   ├── manage_elections.php    # Election CRUD
│   ├── create_election.php     # New election form
│   ├── toggle_election.php     # Active/Inactive toggle logic
│   ├── manage_candidates.php   # Candidate CRUD + image upload
│   ├── manage_voters.php       # Voter list + export trigger
│   ├── add_voter.php           # Manual voter addition
│   ├── results.php             # Live vote counting and winner highlights
│   ├── export_results.php      # Excel generator for results
│   ├── feedback.php            # Inbox for voter feedback
│   ├── reset_requests.php      # Password reset management
│   ├── logs.php                # Audit trail
│   ├── about.php               # System info
│   └── includes/
│       ├── header.php          # Admin Sidebar + Topbar
│       └── footer.php          # Admin Footer
│
└── voter/                      # VOTER PORTAL
    ├── dashboard.php           # Available elections list
    ├── login.php / logout.php  # Auth flow
    ├── register.php            # Signup form
    ├── forgot_password.php     # Password reset request form
    ├── profile.php             # Account management
    ├── vote.php                # Voting ballot interface
    ├── vote_success.php        # Post-vote confirmation
    ├── already_voted.php       # Guard page for completed votes
    ├── feedback.php            # Feedback submission form
    └── includes/
        ├── header.php          # Voter Navbar
        └── footer.php          # Voter Footer
```

---

## 🚀 Setup Instructions

1. **Database Setup:**
   - Create a MySQL database named `aws_voting`.
   - Import `database/aws_voting.sql` into your database.
   
2. **Environment Configuration:**
   - The `.env` file in the root directory manages your DB credentials:
     ```env
     DB_HOST=localhost
     DB_USER=root
     DB_PASS=
     DB_NAME=aws_voting
     ```
   - Adjust these if your local setup differs.

3. **Admin Access:**
   - **URL:** `http://localhost/aws-voting-advanced/admin/login.php`
   - **Default Admin Credentials:** (Check the `admins` table in the SQL file, usually `Vaibhav` / `1234` or similar based on your setup).

---

## 🎨 UI/UX Design System

- **Fonts:** Uses Google Fonts (`Inter` and `Poppins`).
- **Icons:** Native emojis are used for lightweight, fast-loading icons.
- **CSS Architecture:** Vanilla CSS utilizing Custom Properties (Variables) for easy theme customization. No heavy frameworks (like Bootstrap) were used, ensuring optimal performance and full structural control.

## 🔒 Security Best Practices Implemented

1. **Prepared Statements:** `bind_param` is used across all `INSERT`, `UPDATE`, `DELETE`, and critical `SELECT` queries.
2. **XSS Protection:** `htmlspecialchars()` is used universally when outputting user-generated content to the DOM.
3. **Password Security:** Passwords are never stored in plaintext (using `password_hash`).
4. **File Upload Security:** Candidate image uploads are verified by extension and MIME type before being saved to the `/uploads/` directory.

---

## 🙌 Credits

**Designed & Developed by [Vaibhav Mungal](https://github.com/Vaibhavmungal)**

🤖 *Code architecture, security hardening, UI design system, Chart.js integration, and documentation were pair-programmed with [Antigravity AI](https://deepmind.google/) — an advanced agentic coding assistant by Google DeepMind.*

---

> *Built with ❤️, PHP, and a lot of CSS variables.*
