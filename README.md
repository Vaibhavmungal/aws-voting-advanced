# 🗳️ VoteSecure — Advanced Online Voting Platform

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777bb4?logo=php&logoColor=white)](https://www.php.net/)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ed?logo=docker&logoColor=white)](https://www.docker.com/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479a1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Terraform](https://img.shields.io/badge/Terraform-AWS%20IaC-844FBA?logo=terraform&logoColor=white)](terraform/)
[![Kubernetes](https://img.shields.io/badge/Kubernetes-Ready-326ce5?logo=kubernetes&logoColor=white)](k8s/)

VoteSecure is a modern, secure, responsive, and **open-source** PHP-based online voting platform built for **colleges, universities, NGOs, clubs, and organisations**. It features an **Admin Panel** with real-time analytics for election management, a secure **Voter Panel** for authenticated ballot casting, and an enterprise **DevOps & Cloud Deployment Architecture** (Docker, Kubernetes, AWS Terraform, Jenkins, and GitHub Actions).

> 🌐 **Live Demo:** [http://13.206.147.173/](http://13.206.147.173/)

### 🔑 Default Credentials (Seed Data)
| Portal | Access URL | Username / Email | Password | Access Level |
|---|---|---|---|---|
| **Admin Panel** | `/admin/login.php` | `Vaibhav` | `1234` | Full Election, Candidate & Voter Management |
| **Voter Portal** | `/voter/login.php` | *(Register any account or use seeded accounts)* | *(set at signup)* | Ballot Casting |
| **phpMyAdmin** *(Docker)* | `http://localhost:8081` | `voting_user` | `voting_secret` | Web Database GUI |

---

## 🚀 Step-by-Step Deployment Guide

Choose your preferred deployment method below:

| Method | Best For | Estimated Time | Complexity |
|---|---|---|---|
| [**1. Docker Compose**](#1--docker-compose-deployment-recommended) | Local testing, single-server production (EC2 / VPS) | 2 minutes | ⭐ Easy |
| [**2. AWS Terraform IaC**](#2-🏗️-aws-cloud-deployment-via-terraform) | High-availability cloud infrastructure (VPC, Bastion, RDS, EC2) | 10 minutes | ⭐⭐⭐ Advanced |
| [**3. Kubernetes Pods**](#3-☸️-kubernetes-k8s-pod-deployment-zero-downtime) | Cloud-native container clusters (EKS, Minikube, K3s) | 3 minutes | ⭐⭐ Intermediate |
| [**4. Jenkins CI/CD**](#4-🏗️-jenkins-cicd-automated-pipeline) | Automated building, security scanning & k8s pod rollouts | Automated | ⭐⭐ Intermediate |
| [**5. GitHub Actions**](#5-🔄-github-actions-cicd-pipeline) | Continuous delivery to server on Git push | Automated | ⭐⭐ Intermediate |
| [**6. LAMP / XAMPP**](#6-💻-traditional-lamp--xampp-setup) | Bare-metal local PHP development without containers | 5 minutes | ⭐ Easy |

---

### 1. 🐳 Docker Compose Deployment (Recommended)

The fastest and most reliable way to run the entire stack (PHP App + MySQL 8.0 + phpMyAdmin) with zero configuration required.

#### Prerequisites:
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows/macOS) or Docker Engine + Docker Compose Plugin (Linux).

#### Step-by-Step Instructions:

```bash
# Step 1: Clone the repository
git clone https://github.com/Vaibhavmungal/aws-voting-advanced.git
cd aws-voting-advanced

# Step 2: (Optional) Prepare Environment Variables
# The compose file includes sensible defaults, but you can customize .env if needed:
cp .env.example .env

# Step 3: Launch the container stack
# For Local Development (includes phpMyAdmin at :8081):
docker compose up -d

# OR For Production Server (optimized multi-stage build, no phpMyAdmin):
docker compose -f docker-compose.prod.yml up -d --build
```

#### Step 4: Verify Deployment:
```bash
# Check container status (should show 'healthy')
docker ps

# Test application health endpoint
curl http://localhost:8080/health.php
```

#### Step 5: Access Application:
- **VoteSecure App:** [http://localhost:8080](http://localhost:8080)
- **Admin Panel:** [http://localhost:8080/admin/login.php](http://localhost:8080/admin/login.php) (User: `Vaibhav`, Pass: `1234`)
- **phpMyAdmin (Dev only):** [http://localhost:8081](http://localhost:8081)
- **Health Check:** [http://localhost:8080/health.php](http://localhost:8080/health.php)

#### Useful Docker Commands:
```bash
# View live application logs
docker compose logs -f app

# Open a shell inside the running container
docker compose exec app bash

# Stop and remove all containers
docker compose down
```

---

### 2. 🏗️ AWS Cloud Deployment via Terraform

Automate the complete AWS production infrastructure provisioning using enterprise-grade modular Terraform.

#### Architecture Created:
- **Custom VPC** (`10.0.0.0/16`) with DNS support and Internet Gateway.
- **Public Subnet** (`10.0.1.0/24`) for Bastion Host & NAT Gateway.
- **Private App Subnet** (`10.0.2.0/24`) for EC2 Docker Application Server.
- **Private DB Subnets** (`10.0.3.0/24`, `10.0.4.0/24`) spanning 2 Availability Zones for RDS Multi-AZ MySQL.
- **Bastion Host** (SSH Jump Server) in the public subnet.
- **Strict Security Groups** enforcing least privilege access.

#### Prerequisites:
- [AWS CLI](https://aws.amazon.com/cli/) installed and authenticated (`aws configure`).
- [Terraform v1.5+](https://developer.hashicorp.com/terraform/install) installed.
- An existing EC2 Key Pair in your AWS target region (e.g. `votesecure-key`).

#### Step-by-Step Instructions:

```bash
# Step 1: Navigate to the terraform directory
cd terraform

# Step 2: Initialize Terraform plugins and modules
terraform init

# Step 3: Create your terraform variable configuration
cp terraform.tfvars.example terraform.tfvars
```

Edit `terraform.tfvars` with your settings:
```hcl
aws_region    = "ap-south-1"          # Your desired AWS region
key_name      = "votesecure-key"      # Your AWS EC2 Key Pair name
my_ip         = "YOUR_PUBLIC_IP/32"   # Your public IP for secure SSH access
db_password   = "YourStrongPassword!" # Production RDS password
enable_rds    = true                  # Set to false to run MySQL inside EC2 Docker
```

```bash
# Step 4: Preview execution plan
terraform plan

# Step 5: Provision the complete AWS infrastructure
terraform apply -auto-approve
```

#### Step 6: Access Deployed Resources:
Once complete, Terraform outputs your endpoints:
```bash
Apply complete! Resources: 24 added, 0 changed, 0 destroyed.

Outputs:
app_url          = "http://<ec2-public-ip-or-dns>"
bastion_ssh      = "ssh -i <your-key.pem> ec2-user@<bastion-ip>"
db_endpoint      = "<rds-endpoint>:3306"
```

#### Connecting via Bastion Jump Host:
```bash
# SSH into private application instance through Bastion host
ssh -i <your-key.pem> -J ec2-user@<bastion-ip> ubuntu@<app-private-ip>
```

#### Teardown AWS Resources:
```bash
terraform destroy -auto-approve
```

> 📖 **Deep Dive:** See [terraform/README.md](terraform/README.md) for full module documentation.

---

### 3. ☸️ Kubernetes (k8s) Pod Deployment (Zero-Downtime)

Deploy VoteSecure into a Kubernetes cluster with multi-pod replication, ConfigMaps, Secrets, persistent volumes, and rolling update strategy.

#### Architecture:
- **Namespace:** `votesecure`
- **Replicas:** 2 pods with `RollingUpdate` (`maxSurge: 1`, `maxUnavailable: 0`).
- **Probes:** Automated Liveness (`/health.php`) and Readiness probes.
- **Service:** Type `LoadBalancer` mapping port `80` to container port `80`.

#### Prerequisites:
- `kubectl` CLI configured to an active cluster (EKS, Minikube, Kind, or K3s).

#### Step-by-Step Instructions:

```bash
# Step 1: Verify cluster connectivity
kubectl cluster-info

# Step 2: Run automated deployment script
# Syntax: ./scripts/deploy-k8s.sh [<image_tag>] [<namespace>]
./scripts/deploy-k8s.sh

# Or specify a custom container image and namespace:
./scripts/deploy-k8s.sh aws-voting:latest votesecure
```

#### Manual Deployment via `kubectl`:
```bash
# 1. Apply Kubernetes manifests
kubectl apply -f k8s/votesecure.yaml

# 2. Trigger zero-downtime rolling update
kubectl set image deployment/votesecure-app app=aws-voting:latest -n votesecure

# 3. Monitor rollout progress
kubectl rollout status deployment/votesecure-app -n votesecure --timeout=180s

# 4. View active pods and service endpoints
kubectl get pods -n votesecure -o wide
kubectl get svc -n votesecure
```

#### Accessing Locally (Port Forwarding):
```bash
kubectl port-forward svc/votesecure-service 8080:80 -n votesecure
# Access via browser at http://localhost:8080
```

---

### 4. 🏗️ Jenkins CI/CD Automated Pipeline

VoteSecure includes a **universal Declarative Jenkins Pipeline** ([Jenkinsfile](Jenkinsfile)) that automatically builds, tests, and deploys the application.

```
[ Checkout Code ] ──▶ [ Resolve Targets ] ──▶ [ PHP Syntax Lint ] ──▶ [ Auto Build Image ] ──▶ [ Trivy Scan ] ──▶ [ Optional Hub Push ] ──▶ [ Deploy to K8s Pods ]
```

#### Features:
- **Zero Hardcoded Usernames**: Builds automatically from the local `Dockerfile`.
- **Universal Docker Hub Support**: If you provide Docker Hub credentials in Jenkins, it automatically pushes to your account; if omitted, it deploys the locally built image to Kubernetes pods directly.
- **Zero-Downtime Kubernetes Deployment**: Executes `kubectl set image` and monitors `kubectl rollout status`.

#### Step-by-Step Jenkins Setup:
1. **(Optional) Add Docker Hub Credentials**:
   - Navigate to **Manage Jenkins > Credentials > System > Global credentials > Add Credentials**.
   - Kind: **Username with password** | **ID**: `dockerhub-credentials`.
2. **Create Pipeline Job**:
   - Create **New Item > Pipeline**.
   - Under **Pipeline Definition**, select **Pipeline script from SCM**.
   - SCM: **Git** | Repository URL: `https://github.com/<your-username>/aws-voting-advanced.git`.
   - Script Path: `Jenkinsfile`.
3. **Run Pipeline**:
   - Click **Build with Parameters** (or **Build Now**).
   - Parameters available:
     - `IMAGE_NAME`: Container image name (default: `aws-voting`).
     - `PUSH_TO_DOCKERHUB`: Set `true` to push to your Docker Hub repository.
     - `DEPLOY_TO_K8S`: Set `true` to roll out directly to Kubernetes pods.
     - `K8S_NAMESPACE`: Target Kubernetes namespace (default: `votesecure`).

---

### 5. 🔄 GitHub Actions CI/CD Pipeline

The included GitHub Actions workflow (`.github/workflows/deploy.yml`) automates building and deployment on push to `main`.

#### How It Works:
1. **Lint & Test**: Runs syntax verification across all PHP files.
2. **Build Docker Image**: Builds an optimized production container image.
3. **Push to Docker Hub**: Publishes images tagged with `:latest` and `:sha-<commit>`.
4. **Deploy to Server**: SSHs into your AWS EC2 instance and triggers `scripts/deploy.sh`.

#### Required GitHub Secrets:
Set these under **Repository Settings > Secrets and variables > Actions**:
| Secret Name | Description | Example |
|---|---|---|
| `DOCKERHUB_USERNAME` | Your Docker Hub account username | `your-docker-username` |
| `DOCKERHUB_TOKEN` | Docker Hub Access Token | `dckr_pat_xxx` |
| `DEPLOY_HOST` | Server IPv4 address | `54.210.12.34` |
| `DEPLOY_USER` | SSH Username | `ubuntu` or `ec2-user` |
| `DEPLOY_SSH_KEY` | Private SSH Key (`.pem`) | `-----BEGIN RSA PRIVATE KEY-----...` |

---

### 6. 💻 Traditional LAMP / XAMPP Setup

If you prefer running without Docker on bare-metal Apache and MySQL:

```bash
# Step 1: Clone the repository into your web root
# For XAMPP Windows: C:/xampp/htdocs/aws-voting-advanced
# For Ubuntu Linux:   /var/www/html/aws-voting-advanced
git clone https://github.com/Vaibhavmungal/aws-voting-advanced.git

# Step 2: Database Setup
# Open MySQL CLI or phpMyAdmin and run:
mysql -u root -p -e "CREATE DATABASE aws_voting;"
mysql -u root -p aws_voting < database/aws_voting.sql

# Step 3: Configure Environment
cp .env.example .env
```

Edit `.env` with your local database credentials:
```env
DB_HOST=localhost
DB_PORT=3306
DB_USER=root
DB_PASS=your_mysql_password
DB_NAME=aws_voting
APP_NAME=VoteSecure
ALLOWED_EMAIL_DOMAIN=all
APP_URL=http://localhost/aws-voting-advanced
```

```bash
# Step 4: Fix File Permissions (Linux)
chown -R www-data:www-data /var/www/html/aws-voting-advanced/
chmod -R 777 /var/www/html/aws-voting-advanced/uploads/
```

#### Step 5: Access the Application:
- Landing Page: `http://localhost/aws-voting-advanced/`
- Admin Login: `http://localhost/aws-voting-advanced/admin/login.php`
- Voter Login: `http://localhost/aws-voting-advanced/voter/login.php`

---

## 📂 Project Structure

```
aws-voting-advanced/
├── .env.example                # Sample environment configuration
├── .gitignore                  # Git exclusions (.env, uploads, terraform state)
├── Dockerfile                  # Multi-stage optimized production Dockerfile
├── Jenkinsfile                 # Declarative multi-stage Jenkins CI/CD pipeline
├── docker-compose.yml          # Local full-stack compose (App + MySQL + phpMyAdmin)
├── docker-compose.prod.yml     # Production compose definition
├── health.php                  # Automated JSON health probe endpoint
├── index.php                   # Public landing page
├── LICENSE                     # MIT Open-Source License
├── README.md                   # This documentation
│
├── .github/workflows/          # GitHub Actions CI/CD workflows
│   └── deploy.yml              # Automated build, push & EC2 deploy pipeline
│
├── k8s/                        # Kubernetes manifests
│   └── votesecure.yaml         # Complete k8s deployment, service, configmap & secrets
│
├── scripts/                    # Deployment automation scripts
│   ├── deploy.sh               # EC2 / VPS production container pull & rollout
│   └── deploy-k8s.sh           # Kubernetes zero-downtime rolling update script
│
├── terraform/                  # Modular Infrastructure-as-Code for AWS
│   ├── provider.tf             # AWS provider definition
│   ├── variables.tf            # Global variables
│   ├── terraform.tfvars.example# Template variable configuration
│   └── modules/
│       ├── vpc/                # Custom VPC, public/private subnets, IGW, NAT
│       ├── security/           # Least privilege Security Groups
│       ├── bastion/            # Public subnet SSH Bastion Jump Host
│       ├── compute/            # Private subnet EC2 instance with Docker bootstrap
│       └── database/           # Multi-AZ RDS MySQL instance & subnet groups
│
├── config/
│   └── database.php            # Database connection handler (reads .env)
│
├── database/
│   └── aws_voting.sql          # Complete MySQL schema + seed data
│
├── assets/css/                 # Responsive stylesheets
│   ├── admin.css               # Admin Portal design system (dark sidebar)
│   └── voter.css               # Voter Portal & Landing Page glassmorphism styles
│
├── admin/                      # Admin Portal
│   ├── login.php               # Admin authentication
│   ├── dashboard.php           # Real-time analytics (Chart.js side-by-side)
│   ├── manage_elections.php    # Election CRUD + status toggling
│   ├── manage_candidates.php   # Candidate CRUD + image upload
│   ├── manage_voters.php       # Voter list (Aadhar/Mobile, filters, search)
│   ├── results.php             # Live vote counting & winner highlights
│   ├── export_results.php      # 1-click Excel results download
│   └── logs.php                # Security audit log
│
└── voter/                      # Voter Portal
    ├── login.php               # Voter authentication
    ├── register.php            # Registration (Aadhar/Mobile verification)
    ├── dashboard.php           # Active election listings
    ├── vote.php                # Ballot casting interface
    └── profile.php             # Voter profile & password management
```

---

## ✨ Features & Architecture

### 🛡️ Security
- **Prepared Statements (MySQLi):** 100% immune to SQL Injection across all database queries.
- **Bcrypt Password Hashing:** Native PHP `password_hash()` and `password_verify()`.
- **Session Protection:** Strict boundaries between voter and admin sessions with redirect guards.
- **Input Sanitization & XSS Protection:** `htmlspecialchars()` applied universally.
- **Environment Isolation:** Database credentials stored securely in `.env` and Kubernetes Secrets.

### 🧑‍🎓 Voter Panel (Theme: Royal Purple + Gold)
- **Registration:** Captures Name, Email, 12-digit Aadhar/ID, 10-digit Mobile, and Password.
- **Domain Restriction:** Configure `ALLOWED_EMAIL_DOMAIN` in `.env` (set `all` or restrict to `@college.ac.in`).
- **Single Vote Enforcement:** Enforced at both database unique constraints and application levels.
- **Modern UI:** Glassmorphism cards, micro-animations, and fluid responsive design.

### 👨‍💻 Admin Panel (Theme: Modern Dark Sidebar)
- **Real-Time Analytics:** Voter participation doughnut chart and election bar chart side by side.
- **Full Election & Candidate Management:** Image upload, candidate bio, election status toggle.
- **Voter Verification:** View Aadhar & mobile details, filter by voted status, live search.
- **Results & Reporting:** Live vote tallies and 1-click Excel export.
- **Audit Logs:** Full logging of sensitive administrative operations.

---

## 🗄️ Database Schema

| Table | Description | Key Fields |
|---|---|---|
| `users` | Registered voters | `id`, `name`, `email`, `password`, `aadhar_number`, `mobile_number`, `role` |
| `admins` | Administrative users | `id`, `username`, `password`, `email`, `created_at` |
| `elections` | Election records | `id`, `title`, `description`, `start_date`, `end_date`, `status` |
| `candidates` | Election candidates | `id`, `election_id`, `name`, `party`, `photo`, `bio` |
| `votes` | Cast ballot records | `id`, `election_id`, `candidate_id`, `user_id`, `voted_at` |
| `audit_logs` | Admin activity logs | `id`, `admin_id`, `action`, `details`, `ip_address`, `timestamp` |

---

## 🧪 Verification & Test Suite

| Test ID | Test Scenario | Expected Outcome | Status |
|---|---|---|---|
| **T-01** | Docker Stack Launch (`docker compose up -d`) | All 3 containers report `healthy` status | ✅ Pass |
| **T-02** | Automated Health Endpoint (`/health.php`) | Returns HTTP 200 with database status `connected` | ✅ Pass |
| **T-03** | Kubernetes Zero-Downtime Rollout | Pods update sequentially (`maxUnavailable: 0`) | ✅ Pass |
| **T-04** | Terraform Syntax & Module Validation | `terraform validate` reports configuration valid | ✅ Pass |
| **T-05** | Voter Duplicate Registration Guard | Rejects existing email or Aadhar numbers | ✅ Pass |
| **T-06** | Double Voting Prevention | User cannot vote twice in the same election | ✅ Pass |
| **T-07** | SQL Injection Immunity | Parameterized prepared statements on all inputs | ✅ Pass |

---

## 👨‍💻 Developer & Maintainers

**Vaibhav Mungal** — [GitHub](https://github.com/Vaibhavmungal)

> *VoteSecure is designed for effortless deployment across Docker, AWS EC2, Kubernetes, and bare-metal servers.*

---

## 📄 License

This project is licensed under the **[MIT License](LICENSE)**. You are free to use, modify, distribute, and integrate this software into educational, commercial, or institutional projects.
