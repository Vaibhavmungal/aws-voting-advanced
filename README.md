# 🗳️ VoteSecure — Advanced Online Voting Platform

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777bb4?logo=php&logoColor=white)](https://www.php.net/)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ed?logo=docker&logoColor=white)](https://www.docker.com/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479a1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](CONTRIBUTING.md)

VoteSecure is a modern, secure, responsive, and **open-source** PHP-based online voting platform built for **colleges, universities, NGOs, clubs, and organisations**. It features an **Admin Panel** with real-time analytics for election management and a secure **Voter Panel** for authenticated ballot casting.

> 🌐 **Live Demo:** [http://13.206.147.173/](http://13.206.147.173/)

### 🔑 Default Credentials (Seed Data)
| Portal | Access URL | Username / Email | Password | Access Level |
|---|---|---|---|---|
| **Admin Panel** | `/admin/login.php` | `Vaibhav` | `1234` | Full Election, Candidate & Voter Management |
| **Voter Portal** | `/voter/login.php` | *(Register any account or use seeded accounts)* | *(set at signup)* | Ballot Casting |
| **phpMyAdmin** *(Docker)* | `http://localhost:8081` | `voting_user` | `voting_secret` | Web Database GUI |

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

## 🐳 Docker Quickstart (Recommended)

Run the entire application stack (PHP App + MySQL Database + phpMyAdmin) with a single command — zero manual configuration required!

```bash
# 1. Clone the repository
git clone https://github.com/Vaibhavmungal/aws-voting-advanced.git
cd aws-voting-advanced

# 2. Start the full stack with Docker Compose
docker compose up -d
```

### Accessing Containerized Services:
| Service | URL | Default Credentials / Info |
|---|---|---|
| **VoteSecure App** | `http://localhost:8080` | Landing page, Voter & Admin portals |
| **phpMyAdmin** | `http://localhost:8081` | Web database UI (Server: `db`, User: `voting_user`, Pass: `voting_secret`) |
| **Health Check** | `http://localhost:8080/health.php` | Returns container & DB connection health in JSON |
| **MySQL 8.0** | Port `3307` | Auto-seeds schema from `database/aws_voting.sql` |

```bash
# View live logs
docker compose logs -f app

# Stop the stack
docker compose down
```

---

## 🔄 CI/CD & Automated DevOps Deployment

VoteSecure includes a production-grade GitHub Actions CI/CD pipeline (`.github/workflows/deploy.yml`) that automatically:
1. **Lints & Validates**: Runs `php -l` across all PHP files to catch any syntax bugs before deployment.
2. **Builds & Pushes**: Compiles the optimized multi-stage Docker image and pushes it to **Docker Hub** tagged with both `:latest` and the commit SHA (`:sha-<git_sha>`).
3. **Automated Deployment**: SSHs into your **AWS EC2** instance / server and runs `scripts/deploy.sh` to pull the latest image and restart containers with minimal downtime.

### 🔑 Setting Up GitHub Secrets
To enable automated Docker Hub pushing and AWS deployment, add the following secrets in your GitHub repository (**Settings > Secrets and variables > Actions > Repository secrets**):

| Secret Name | Description | Example |
|---|---|---|
| `DOCKERHUB_USERNAME` | Your Docker Hub account username | `vaibhavmungal` |
| `DOCKERHUB_TOKEN` | Docker Hub Personal Access Token (Read & Write) | `dckr_pat_xxx` |
| `DOCKERHUB_REPO` *(Optional)* | Custom Docker Hub repo name | `vaibhavmungal/aws-voting` |
| `DEPLOY_HOST` | AWS EC2 Public IPv4 or DNS | `54.210.12.34` |
| `DEPLOY_USER` | EC2 SSH username | `ubuntu` or `ec2-user` |
| `DEPLOY_SSH_KEY` | Private SSH key (`.pem`) used to connect to EC2 | `-----BEGIN RSA PRIVATE KEY-----...` |
| `DEPLOY_PATH` *(Optional)* | Project folder path on server | `/opt/aws-voting-advanced` (default) |

---

### 🏗️ Jenkins CI/CD Pipeline (`Jenkinsfile`)

VoteSecure includes a universal, enterprise-grade **Declarative Jenkins Pipeline** ([Jenkinsfile](Jenkinsfile)) supporting multi-stage continuous integration and Kubernetes pod delivery:

```
[ Checkout Code ] ──▶ [ Resolve Config ] ──▶ [ PHP Syntax Check ] ──▶ [ Docker Build ] ──▶ [ Trivy Scan ] ──▶ [ Push to Docker Hub ] ──▶ [ Deploy to Kubernetes Pods ]
```

#### 🌟 Universal Multi-User Support (Zero Code Changes Needed):
Any developer or organisation who clones or forks this repository can run the pipeline without modifying code:
- **Automatic Username Detection**: The pipeline automatically reads your Docker Hub username from your Jenkins credentials!
- **Parameter Override**: You can also supply `DOCKERHUB_USERNAME` directly in the build parameters.

#### ⚙️ Pipeline Parameters:
| Parameter | Default Value | Description |
|---|---|---|
| `DOCKERHUB_USERNAME` | *(empty)* | Leave blank to auto-detect username from Jenkins credentials, or specify your username |
| `DOCKER_REPO_NAME` | `aws-voting` | Your Docker Hub repository name |
| `DOCKERHUB_CREDENTIALS_ID` | `dockerhub-credentials` | Jenkins Credential ID (Username with password) |
| `DEPLOY_TO_K8S` | `true` | Deploy the updated image directly into Kubernetes pods |
| `K8S_NAMESPACE` | `votesecure` | Kubernetes target namespace |
| `K8S_CONFIG_CREDENTIALS_ID` | *(empty)* | Optional Jenkins Secret File credential ID for kubeconfig |

#### ☸️ Kubernetes Pod Deployment & Zero-Downtime Rollouts:
When `DEPLOY_TO_K8S` is active, the pipeline:
1. Validates cluster connectivity and ensures namespace `votesecure` exists.
2. Applies manifests from `k8s/votesecure.yaml`.
3. Performs a rolling pod update: `kubectl set image deployment/votesecure-app app=<USER>/aws-voting:<BUILD_NUMBER>`
4. Monitors health probes (`/health.php`) with zero-downtime strategy (`maxSurge: 1`, `maxUnavailable: 0`).
5. Displays active pods and exposed services.

#### 🛠️ Manual Kubernetes Deployment:
You can also deploy to your Kubernetes cluster directly using the helper script:
```bash
# Deploy latest image to default 'votesecure' namespace
./scripts/deploy-k8s.sh

# Deploy specific image tag to a custom namespace
./scripts/deploy-k8s.sh <your-username>/aws-voting:42 production
```

#### 📋 How to Configure in Jenkins:
1. **Add Docker Hub Credentials**:
   - Navigate to **Manage Jenkins > Credentials > System > Global credentials > Add Credentials**.
   - Kind: **Username with password**.
   - **ID**: `dockerhub-credentials`
   - **Username**: Your Docker Hub username.
   - **Password**: Your Docker Hub Personal Access Token.
2. **Create Pipeline Job**:
   - Create a **New Item > Pipeline** (or **Multibranch Pipeline**).
   - In **Pipeline definition**, select **Pipeline script from SCM**.
   - Choose **Git** and enter your repository URL: `https://github.com/<your-username>/aws-voting-advanced.git`.
   - Script Path: `Jenkinsfile`.
3. **Run Pipeline**:
   - Click **Build with Parameters** (or **Build Now**). The pipeline will automatically lint, build, tag, push to your Docker Hub repository, and deploy into your Kubernetes pods!

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

## 🏗️ Infrastructure as Code (Terraform for AWS)

Provision the complete AWS cloud architecture (VPC, Subnets, Security Groups, EC2 with automated Docker bootstrap, and optional RDS MySQL) with a single command!

```bash
# 1. Navigate to terraform directory
cd terraform

# 2. Copy and customize configuration
cp terraform.tfvars.example terraform.tfvars

# 3. Initialize & Deploy to AWS
terraform init
terraform apply
```

> 📖 **Full Terraform Guide:** See [terraform/README.md](terraform/README.md) for architecture details, variable options, and teardown instructions.

---

## ☁️ AWS EC2 Deployment (Manual)

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

## 👨‍💻 Developer & Maintainers

**Vaibhav Mungal** — [GitHub](https://github.com/Vaibhavmungal)

> *Built with ❤️, PHP, MySQL, and modern CSS variables. Designed for effortless deployment on Docker, AWS EC2, and Kubernetes.*

---

## 🤝 Contributing

Contributions are warmly welcomed! VoteSecure is an open-source project and thrives on community feedback and code contributions.

- Review our [Contributing Guide](CONTRIBUTING.md) to get started with our workflow and coding conventions.
- Check out our [Code of Conduct](CODE_OF_CONDUCT.md) for community standards.
- Feel free to open an issue or submit a pull request!

---

## 📄 License

This project is open-source and licensed under the **[MIT License](LICENSE)**. You are free to use, modify, distribute, and integrate this software into your own college, organisation, or commercial projects.

