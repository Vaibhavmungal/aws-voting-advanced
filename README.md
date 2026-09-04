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

## 📋 Required Software & Version Matrix

Below are the recommended and minimum supported versions along with official direct download links for all technologies, runtimes, container engines, and DevOps tools used across VoteSecure:

| Category | Software / Tool | Recommended Version | Minimum Version | Official Download Link | Purpose in VoteSecure |
|---|---|---|---|---|---|
| **Container Engine** | **Docker Engine / Desktop** | `25.0+` / `24.0+` | `20.10+` | [Download Docker](https://www.docker.com/products/docker-desktop/) | Packaging PHP app and isolated service runtime |
| **Orchestration** | **Docker Compose** | `v2.24+` / `v2.20+` | `v2.0.0+` | [Download Compose](https://docs.docker.com/compose/install/) | Multi-container stack (App + MySQL + phpMyAdmin) |
| **CI/CD Server** | **Jenkins** | `2.426+` (LTS) | `2.400+` | [Download Jenkins](https://www.jenkins.io/download/) | CI/CD automation: lint, test, Trivy scan, and k8s pod rollout |
| **Cloud Native** | **Kubernetes (k8s)** | `v1.28+` / `v1.29+` | `v1.24+` | [Download Minikube/K8s](https://kubernetes.io/docs/tasks/tools/) | Container pod scaling, self-healing, and rolling updates |
| **K8s CLI** | **kubectl** | `v1.28+` | `v1.24+` | [Download kubectl](https://kubernetes.io/docs/tasks/tools/install-kubectl/) | Command-line control plane client for cluster deployments |
| **Cloud IaC** | **Terraform** | `v1.7+` | `v1.5.0+` | [Download Terraform](https://developer.hashicorp.com/terraform/install) | Automated AWS infrastructure (VPC, Bastion, EC2, RDS) |
| **Cloud Provider** | **AWS CLI** | `v2.15+` | `v2.0+` | [Download AWS CLI](https://aws.amazon.com/cli/) | AWS command-line authentication and configuration |
| **Backend Runtime**| **PHP** | `8.2+` / `8.3` | `8.1+` | [Download PHP](https://www.php.net/downloads) | Core backend logic (`mysqli`, `pdo`, `mbstring`, `curl`) |
| **Database** | **MySQL Server** | `8.0+` | `8.0.28+` | [Download MySQL](https://dev.mysql.com/downloads/mysql/) | Relational database (elections, candidates, voters & votes) |
| **Web Server** | **Apache HTTP Server** | `2.4.57+` | `2.4+` | [Download Apache](https://httpd.apache.org/download.cgi) | Web server with `mod_rewrite` URL routing (or via [XAMPP](https://www.apachefriends.org/download.html)) |
| **VCS** | **Git** | `2.40+` | `2.30+` | [Download Git](https://git-scm.com/downloads) | Source code versioning and pipeline checkout |
| **Security Scanner**| **Trivy** *(Optional)* | `0.48+` | `0.38+` | [Download Trivy](https://github.com/aquasecurity/trivy/releases) | Container vulnerability assessment in CI/CD pipeline |

> 💡 **Quick Install via Package Managers:**
> - **Windows (Winget):** `winget install Docker.DockerDesktop Hashicorp.Terraform Amazon.AWSCLI Git.Git Kubernetes.kubectl`
> - **macOS (Homebrew):** `brew install docker docker-compose terraform awscli kubectl git php mysql`
> - **Ubuntu / Debian (APT):** `sudo apt update && sudo apt install -y docker.io docker-compose-v2 git php8.2 mysql-server`

---

## 📥 Prerequisites & Service Installation (AWS EC2 / Ubuntu 24.04 LTS)

> All cloud infrastructure and CI/CD platforms — **AWS EC2**, **Docker**, **Jenkins**, and **Kubernetes** — run on **Linux (Ubuntu 24.04 LTS)**. Use these copy-paste commands to set up your AWS EC2 instance from scratch.

---

### 1. 🔧 Git

```bash
sudo apt update
sudo apt install -y git

# Verify
git --version
# git version 2.x.x
```

---

### 2. 🐘 PHP 8.2 + Required Extensions

```bash
sudo apt update
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

sudo apt install -y \
  php8.2 \
  php8.2-cli \
  php8.2-mysql \
  php8.2-gd \
  php8.2-zip \
  php8.2-mbstring \
  php8.2-xml \
  php8.2-curl \
  php8.2-opcache \
  php8.2-mysqli

# Verify
php --version
# PHP 8.2.x
```

---

### 3. 🐬 MySQL 8.0

```bash
sudo apt update
sudo apt install -y mysql-server

sudo systemctl start mysql
sudo systemctl enable mysql

# Secure the installation (set root password, remove test DBs)
sudo mysql_secure_installation

# Create database and import VoteSecure schema
sudo mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS aws_voting;"
sudo mysql -u root -p aws_voting < database/aws_voting.sql

# Verify
mysql --version
# mysql  Ver 8.0.x
```

---

### 4. 🌐 Apache 2.4

```bash
sudo apt update
sudo apt install -y apache2

# Enable URL rewriting (needed for clean routes)
sudo a2enmod rewrite
sudo systemctl restart apache2
sudo systemctl enable apache2

# Set correct permissions for web root
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html

# Verify
apache2 -v
# Server version: Apache/2.4.x
```

---

### 5. 🐳 Docker & Docker Compose (Ubuntu 24.04 LTS)

> Required to run the containerized stack (`votesecure_app` + `votesecure_db` + `votesecure_phpmyadmin`).

```bash
# Install dependencies
sudo apt update
sudo apt install -y ca-certificates curl gnupg lsb-release

# Add Docker's official GPG key for Ubuntu 24.04
sudo install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
sudo chmod a+r /etc/apt/keyrings/docker.gpg

# Add Docker repository for Ubuntu 24.04 (noble)
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
  $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | \
  sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Install Docker Engine + Compose plugin
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

# Allow running Docker without sudo
sudo usermod -aG docker $USER
newgrp docker

# If running Jenkins on the same server, grant Jenkins docker privileges:
sudo usermod -aG docker jenkins 2>/dev/null || true
sudo systemctl restart docker

# Start and enable Docker on boot
sudo systemctl start docker
sudo systemctl enable docker

# Verify
docker --version          # Docker version 25.x.x / 24.x.x
docker compose version    # Docker Compose version v2.x.x
```

---

### 6. 🏗️ Jenkins (Self-Hosted CI/CD on Ubuntu 24.04)

> Install Jenkins on your AWS EC2 Ubuntu 24.04 instance to automate the entire build, test, and Kubernetes deployment pipeline.

```bash
# Install Java (Jenkins requires JDK 17 or 21)
sudo apt update
sudo apt install -y openjdk-17-jdk

java -version
# openjdk version "17.x.x"

# Add official Jenkins repository key and repo
sudo install -m 0755 -d /usr/share/keyrings
curl -fsSL https://pkg.jenkins.io/debian-stable/jenkins.io-2023.key | sudo tee /usr/share/keyrings/jenkins-keyring.asc > /dev/null

echo "deb [signed-by=/usr/share/keyrings/jenkins-keyring.asc] https://pkg.jenkins.io/debian-stable binary/" | sudo tee /etc/apt/sources.list.d/jenkins.list > /dev/null

# Install Jenkins
sudo apt update
sudo apt install -y jenkins

# Grant Jenkins user access to Docker daemon
sudo usermod -aG docker jenkins
sudo systemctl restart jenkins

# Start and enable Jenkins
sudo systemctl start jenkins
sudo systemctl enable jenkins

# Retrieve initial admin unlock password
sudo cat /var/lib/jenkins/secrets/initialAdminPassword

# Access Jenkins at: http://<your-ec2-public-ip>:8080

# 💡 PIPELINE DEPENDENCIES:
# Install PHP syntax checking dependencies on Jenkins host:
sudo apt update
sudo apt install -y php-cli php-xml php-mbstring php-zip php-gd php-mysql php-curl
```

---

### 7. ☸️ Kubernetes (kubectl CLI on Ubuntu 24.04)

> Required to manage and trigger zero-downtime rolling deployments to Kubernetes pods.

```bash
# Download official Kubernetes signing key
sudo install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://pkgs.k8s.io/core:/stable:/v1.29/deb/Release.key | sudo gpg --dearmor -o /etc/apt/keyrings/kubernetes-apt-keyring.gpg
sudo chmod 644 /etc/apt/keyrings/kubernetes-apt-keyring.gpg

# Add Kubernetes apt repository
echo 'deb [signed-by=/etc/apt/keyrings/kubernetes-apt-keyring.gpg] https://pkgs.k8s.io/core:/stable:/v1.29/deb/ /' | sudo tee /etc/apt/sources.list.d/kubernetes.list
sudo chmod 644 /etc/apt/sources.list.d/kubernetes.list

# Install kubectl
sudo apt update
sudo apt install -y kubectl

# Verify
kubectl version --client
# Client Version: v1.29.x
```

---

### 8. 🏗️ HashiCorp Terraform & AWS CLI v2 (Ubuntu 24.04)

> Required for automated Infrastructure-as-Code provisioning of AWS VPC, Bastion, EC2, and RDS.

```bash
# Install HashiCorp Terraform
wget -O- https://apt.releases.hashicorp.com/gpg | sudo gpg --dearmor -o /usr/share/keyrings/hashicorp-archive-keyring.gpg
echo "deb [signed-by=/usr/share/keyrings/hashicorp-archive-keyring.gpg] https://apt.releases.hashicorp.com $(lsb_release -cs) main" | sudo tee /etc/apt/sources.list.d/hashicorp.list
sudo apt update && sudo apt install -y terraform

# Install AWS CLI v2
sudo apt install -y unzip curl
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
unzip -q awscliv2.zip
sudo ./aws/install --update
rm -rf aws awscliv2.zip

# Verify
terraform --version
aws --version
```

---

### 9. ✅ Verify All Services (Ubuntu 24.04 LTS)

Run this single command block on your AWS EC2 instance to confirm all prerequisites are installed and operating:

```bash
git --version            # git version 2.x.x
php --version            # PHP 8.2.x
mysql --version          # mysql  Ver 8.0.x
apache2 -v               # Server version: Apache/2.4.x
docker --version         # Docker version 25.x.x / 24.x.x
docker compose version   # Docker Compose version v2.x.x
java -version            # openjdk version "17.x.x"  (for Jenkins)
jenkins --version        # Jenkins 2.x.x
kubectl version --client # Client Version: v1.29.x
terraform --version      # Terraform v1.7.x
aws --version            # aws-cli/2.x.x
```

---

## ⚙️ How the Project Works (Step-by-Step Architecture & Lifecycle)

VoteSecure connects administrators, voters, database engines, and automated DevOps infrastructure into a unified, secure online election system.

### 🎨 3D Interactive & Animated Architecture Diagram

![VoteSecure 3D Cloud Architecture](assets/images/aws-architecture-3d.svg)

### 1️⃣ Phase 1: System Initialization & Bootstrapping
1. **Container / Server Startup**: Docker Compose, Kubernetes, or Apache initializes PHP 8.2 with required extensions (`mysqli`, `mbstring`, `curl`).
2. **Database Auto-Seeding**: MySQL initializes schema from `database/aws_voting.sql`, creating tables (`users`, `admins`, `elections`, `candidates`, `votes`, `audit_logs`) and seeding the initial administrator account (`Vaibhav` / `1234`).
3. **Environment Isolation**: `config/database.php` reads database credentials and settings (such as allowed email domains) securely from environment variables (`.env` or Kubernetes secrets).
4. **Health Check Probe**: `health.php` verifies backend runtime and MySQL database connectivity, returning a JSON status `{"status": "healthy", "database": "connected"}` for Kubernetes and Docker liveness checks.

---

### 2️⃣ Phase 2: Administrator Workflow (Election Management)
1. **Admin Authentication**: Admin logs into `/admin/login.php` with Bcrypt password verification. The system generates a dedicated admin session (`$_SESSION['admin']`) and logs the login IP address in `audit_logs`.
2. **Election Configuration**: Admin visits `/admin/manage_elections.php` to create elections, defining titles, election categories, date windows, and toggling active/inactive status.
3. **Candidate Registration**: Admin navigates to `/admin/manage_candidates.php` to register candidates for specific elections, uploading profile pictures (saved to `/uploads/`) and writing candidate biographies and party affiliations.
4. **Voter Verification & Roster Control**: Admin monitors registered voters on `/admin/manage_voters.php`, reviewing 12-digit Aadhar IDs, 10-digit mobile numbers, and voting participation indicators. Admin can also manually enroll voters (`add_voter.php`).
5. **Real-Time Analytics Dashboard**: `/admin/dashboard.php` renders side-by-side Chart.js visualizations (Voter turnout doughnut chart + votes-per-election bar chart) with live calculations from the database.
6. **Results & Reporting**: Live election outcomes are tallied on `/admin/results.php` with automatic winner highlights. Admin can download full audited spreadsheets via `/admin/export_results.php`.

---

### 3️⃣ Phase 3: Voter Journey (Secure Ballot Casting)
1. **Voter Registration**: 
   - New voters register at `/voter/register.php` with their Name, Email, 12-digit Aadhar/ID Number, 10-digit Mobile Number, and Password.
   - The backend validates strict regex for Aadhar (12 numeric digits) and Mobile (10 digits).
   - Email domain rules configured via `ALLOWED_EMAIL_DOMAIN` are enforced (supports `all` or institution-specific domains like `@college.ac.in`).
   - Duplicate prevention checks verify that neither the email nor the Aadhar number is already in use.
   - Passwords are encrypted using PHP's native `password_hash($pass, PASSWORD_BCRYPT)`.
2. **Voter Authentication**: Voters log in at `/voter/login.php`. Session guards protect all `/voter/` routes, redirecting unauthenticated traffic to login.
3. **Election Exploration**: The voter dashboard (`/voter/dashboard.php`) fetches all currently active elections and displays their start/end dates.
4. **Entering the Voting Booth**: Clicking "Vote Now" opens `/voter/vote.php` for the selected election. The platform presents responsive candidate cards featuring photos, names, parties, and bios.
5. **Atomic Ballot Submission**: 
   - The voter selects their candidate and submits their choice.
   - The backend begins a database transaction:
     - Checks if the voter has already voted in this election.
     - Inserts the ballot record into `votes` table (`election_id`, `candidate_id`, `user_id`, `voted_at`).
     - Updates the voter's participation record.
     - Commits the transaction.
6. **Duplicate Voting Lock**: If a voter attempts to vote in the same election again, database uniqueness checks and UI guards intercept the request and redirect immediately to `/voter/already_voted.php`.
7. **Post-Vote Confirmation**: Voter sees a confirmation screen (`/voter/vote_success.php`) and can view their profile or submit feedback to administrators (`/voter/feedback.php`).

---

### 4️⃣ Phase 4: Automated DevOps & Deployment Workflow
1. **Code Commit**: A developer commits code and pushes to GitHub.
2. **Continuous Integration**:
   - **GitHub Actions** (`.github/workflows/deploy.yml`) or **Jenkins Pipeline** (`Jenkinsfile`) triggers automatically.
   - Runs syntax validation (`php -l`) across all PHP files.
   - Builds optimized multi-stage Docker image from `Dockerfile`.
   - Runs vulnerability scan with Trivy for CVE mitigation.
3. **Continuous Delivery & Zero-Downtime Rollout**:
   - **Kubernetes**: Deploys via `scripts/deploy-k8s.sh` using rolling updates (`maxSurge: 1, maxUnavailable: 0`). A new container pod spins up, passes the `/health.php` readiness probe, and only then is the old pod retired.
   - **AWS Cloud**: Terraform provisions high-availability multi-tier infrastructure (custom VPC, private subnets, Bastion host, EC2 compute, and RDS MySQL) with automated Docker bootstrap.

---

## ☁️ AWS Cloud Infrastructure & Component Breakdown

The AWS cloud deployment utilizes a battle-tested **Multi-AZ 3-Tier Enterprise Architecture** adhering strictly to AWS Well-Architected Framework principles (Security, Reliability, Performance Efficiency, and Cost Optimization):

| AWS Service / Component | Layer / Tier | Role & Purpose in VoteSecure | Key Configuration Details |
|---|---|---|---|
| **🌐 Amazon VPC** | Network Isolation | Private virtual network encapsulating all infrastructure | CIDR: `10.0.0.0/16`, DNS resolution & hostnames enabled |
| **🚪 Internet Gateway (IGW)** | Edge Ingress/Egress | Connects public subnet resources directly to the open internet | Attached to VPC, default route for `0.0.0.0/0` in public route table |
| **🔄 AWS NAT Gateway** | Egress Translation | Provides outbound internet for private EC2 instances without exposing them | Deployed in Public Subnet with static Elastic IP (EIP) |
| **🏢 Public Subnet (`10.0.1.0/24`)** | DMZ Tier | Hosts edge-facing services: ALB, Bastion Jump Host, and NAT Gateway | `map_public_ip_on_launch = true`, AZ: `ap-south-1a` |
| **🛡️ Application Load Balancer (ALB)** | Traffic Distribution | Distributes incoming HTTPS/HTTP traffic across multi-AZ container pods | Port 80/443 listener, TLS 1.3 termination, Health probe: `/health.php` |
| **🏰 Bastion Jump Host (EC2)** | Security Management | Secure bastion jump box for authenticated admin SSH tunneling | `t3.micro` EC2 in public subnet, restricted by SSH IP security group |
| **⚡ Private App Subnet (`10.0.2.0/24`)** | Compute Tier | Houses the containerized application EC2 instances and pods | Isolated from public ingress; routes outbound traffic through NAT Gateway |
| **🐳 EC2 Compute & ASG** | Compute Engine | Runs VoteSecure Docker containers and Kubernetes nodes | Ubuntu 22.04 LTS (`t3.small`/`t3.medium`), automated bootstrap user-data |
| **🗄️ Private DB Subnet 1 (`10.0.3.0/24`)**| Data Tier (AZ-1) | Primary subnet for Amazon RDS MySQL database | AZ: `ap-south-1a`, completely air-gapped from internet access |
| **🗄️ Private DB Subnet 2 (`10.0.4.0/24`)**| Data Tier (AZ-2) | Secondary availability zone for RDS Multi-AZ standby replica | AZ: `ap-south-1b`, meets AWS DB Subnet Group multi-AZ requirement |
| **🔄 Amazon RDS MySQL 8.0** | Database Tier | Managed relational database engine for voters, votes, and elections | Engine 8.0, Multi-AZ sync replication, 60s auto-failover, 20GB gp3 storage |
| **📦 Amazon S3 Bucket** | Object Storage | Highly durable storage for candidate profile images and database snapshots | AES-256 SSE encryption, private bucket policies, pre-signed upload URLs |
| **🌐 AWS Route 53** | Global DNS | Highly available cloud DNS service with health checks | Low-latency Anycast routing, alias records pointing to ALB |
| **🛡️ AWS WAF & Shield** | Perimeter Security | Blocks SQL Injection, Cross-Site Scripting (XSS), and Layer 7 DDoS | Managed rule sets for OWASP Top 10 vulnerabilities |
| **📊 Amazon CloudWatch** | Observability | Real-time metric collection, alarms, and container log aggregation | CPU/Memory utilization alarms, `/health.php` uptime monitoring, SNS alerts |
| **🔐 AWS Secrets Manager & KMS** | Secret Security | Stores database passwords, encryption keys, and environment variables | Automated secret rotation and envelope encryption via AWS KMS |
| **🔒 Security Groups (Firewalls)** | Network Security | Stateful virtual firewalls implementing strict least-privilege traffic rules | Distinct groups for Bastion, ALB, App Compute, and RDS Database |

### 🔒 Security Group Firewall Matrix

| Security Group | Inbound Rules | Outbound Rules | Purpose |
|---|---|---|---|
| **`bastion-sg`** | Port `22` (SSH) from `YOUR_IP/32` only | All traffic (`0.0.0.0/0`) | Secure administrator jump access |
| **`alb-sg`** | Port `80` (HTTP) & `443` (HTTPS) from `0.0.0.0/0` | Port `80` to `app-sg` | Public web ingress |
| **`app-sg`** | Port `80` from `alb-sg`, Port `22` from `bastion-sg` | All traffic via NAT Gateway | Application container compute |
| **`db-sg`** | Port `3306` (MySQL) from `app-sg` only | None (Air-gapped) | Zero direct internet exposure for voter data |

---

## 🚀 Automated CI/CD Deployment Guide (Step-by-Step Setup)

VoteSecure features two enterprise-grade continuous integration and continuous deployment (CI/CD) pipelines designed for automated testing, container packaging, security scanning, and zero-downtime rollouts:

| Pipeline | Automation Tool | Trigger | Deployment Target | Key Capabilities |
|---|---|---|---|---|
| [**1. Jenkins CI/CD Pipeline**](#1-🏗️-jenkins-cicd-pipeline-setup--deployment) | Jenkins (Self-Hosted on EC2) | Webhook / Manual "Build with Parameters" | **Kubernetes Pods & Docker** | PHP syntax check, multi-stage Docker build, Trivy CVE scan, dynamic Docker Hub push, zero-downtime k8s rolling rollout |
| [**2. GitHub Actions CI/CD**](#2-🔄-github-actions-cicd-pipeline-setup--deployment) | GitHub Cloud Runners | Git push to `main` branch | **AWS EC2 Production Server** | Automated PHP lint, Docker image push to Docker Hub (`:latest` & `:sha`), SSH automated zero-downtime deployment |

---

### 1. 🏗️ Jenkins CI/CD Pipeline Setup & Deployment

The VoteSecure **Declarative Jenkins Pipeline** ([Jenkinsfile](Jenkinsfile)) provides end-to-end automation from code checkout to Kubernetes pod rollout.

```text
[ Checkout SCM ] ──▶ [ Resolve Targets ] ──▶ [ PHP Syntax Check ] ──▶ [ Auto Build Image ] ──▶ [ Trivy Scan ] ──▶ [ Optional Hub Push ] ──▶ [ Deploy to K8s Pods ]
```

#### 📋 Step-by-Step Jenkins Setup Guide (From Scratch on AWS EC2):

##### Step 1: Install Jenkins on AWS EC2 (Ubuntu 24.04 LTS)
If you haven't installed Jenkins yet, run these commands on your EC2 instance (or refer to the Prerequisites section above):
```bash
# 1. Install Java 17
sudo apt update && sudo apt install -y openjdk-17-jdk

# 2. Add Jenkins official repository
sudo install -m 0755 -d /usr/share/keyrings
curl -fsSL https://pkg.jenkins.io/debian-stable/jenkins.io-2023.key | sudo tee /usr/share/keyrings/jenkins-keyring.asc > /dev/null
echo "deb [signed-by=/usr/share/keyrings/jenkins-keyring.asc] https://pkg.jenkins.io/debian-stable binary/" | sudo tee /etc/apt/sources.list.d/jenkins.list > /dev/null

# 3. Install and start Jenkins
sudo apt update && sudo apt install -y jenkins
sudo systemctl enable --now jenkins

# 4. Grant Jenkins user permission to run Docker commands
sudo usermod -aG docker jenkins
sudo systemctl restart jenkins

# 5. Retrieve initial admin unlock password
sudo cat /var/lib/jenkins/secrets/initialAdminPassword
```
> ⚠️ **AWS Security Group Requirement:** Ensure port `8080` (TCP) is opened in your EC2 instance Security Group to access the Jenkins web dashboard.

---

##### Step 2: Unlock Jenkins & Install Recommended Plugins
1. Open your browser and navigate to: `http://<your-ec2-public-ip>:8080`.
2. Paste the **Administrator Password** retrieved from `/var/lib/jenkins/secrets/initialAdminPassword`.
3. Select **"Install suggested plugins"** and allow the installer to complete.
4. Create your Admin user (e.g. Username: `admin`, Password: `<your-password>`).
5. (Recommended) Go to **Manage Jenkins > Plugins > Available plugins**, search for and install:
   - **Docker Pipeline**
   - **Pipeline: Stage View**
   - **Kubernetes CLI** (if connecting via Kubeconfig credentials)

---

##### Step 3: Configure Docker Hub Credentials (Optional for Pushing)
The pipeline dynamically reads your Docker Hub credentials without hardcoding any usernames:
1. Navigate to **Manage Jenkins > Credentials > System > Global credentials > Add Credentials**.
2. Set **Kind**: `Username with password`.
3. Set **Scope**: `Global`.
4. Set **ID**: `dockerhub-credentials` *(Must match the default parameter)*.
5. Enter your Docker Hub **Username** and **Personal Access Token** (or Password).
6. Click **Create**.

---

##### Step 4: Create the VoteSecure Pipeline Job
1. From the Jenkins Dashboard, click **New Item**.
2. Enter Item Name: `votesecure-pipeline` and choose **Pipeline**, then click **OK**.
3. Under the **General** tab, check **This project is parameterized** (The pipeline will auto-detect parameters from `Jenkinsfile` on first run).
4. Scroll down to the **Pipeline** section:
   - **Definition**: Select **Pipeline script from SCM**.
   - **SCM**: Select **Git**.
   - **Repository URL**: `https://github.com/Vaibhavmungal/aws-voting-advanced.git` (or your forked repository URL).
   - **Branch Specifier**: `*/main`.
   - **Script Path**: `Jenkinsfile`.
5. Click **Save**.

---

##### Step 5: (Optional) Configure Webhook for Automated Trigger on Git Push
1. In your Jenkins Job configuration, under **Build Triggers**, check:
   - **GitHub hook trigger for GITScm polling** OR
   - **Poll SCM** (Schedule: `H/5 * * * *` to check every 5 minutes).
2. On GitHub (**Repository > Settings > Webhooks > Add Webhook**):
   - **Payload URL**: `http://<your-ec2-ip>:8080/github-webhook/`
   - **Content type**: `application/json`
   - **Events**: Just the `push` event.

---

##### Step 6: Execute the Pipeline ("Build with Parameters")
1. Click **Build with Parameters** in the left sidebar.
2. Review/customize the build parameters:
   | Parameter | Default | Purpose |
   |---|---|---|
   | `IMAGE_NAME` | `aws-voting` | Container image name (built from Dockerfile) |
   | `DOCKERHUB_USERNAME` | *(blank)* | Auto-detected from Jenkins credentials, or override with your username |
   | `DOCKERHUB_CREDENTIALS_ID` | `dockerhub-credentials` | Jenkins credentials ID for Docker Hub |
   | `PUSH_TO_DOCKERHUB` | `false` | Check `true` to push tagged image to Docker Hub |
   | `DEPLOY_TO_K8S` | `true` | Deploy image directly to Kubernetes pods |
   | `K8S_NAMESPACE` | `votesecure` | Target Kubernetes namespace |
3. Click **Build**.

---

##### Step 7: Automated Execution & Verification
Watch the live pipeline execution stages:
1. **📥 Checkout Code**: Clones the latest commit from Git.
2. **⚙️ Resolve Configuration**: Resolves image names without hardcoded usernames.
3. **🔍 Lint & PHP Syntax Check**: Validates syntax across all PHP files via `php -l`.
4. **🐳 Build Docker Image**: Multi-stage compilation creates `aws-voting:${BUILD_NUMBER}` and `aws-voting:latest`.
5. **🛡️ Security Scan (Trivy)**: Scans container image for vulnerabilities.
6. **📤 Push to Docker Hub** *(If enabled)*: Authenticates and publishes images.
7. **☸️ Deploy to Kubernetes Pods**: Executes rolling update (`kubectl set image deployment/votesecure-app app=aws-voting:${BUILD_NUMBER} -n votesecure`) and monitors rollout completion with zero downtime (`maxSurge: 1, maxUnavailable: 0`).

---

### 2. 🔄 GitHub Actions CI/CD Pipeline Setup & Deployment

The included GitHub Actions workflow (`.github/workflows/deploy.yml`) provides instant continuous deployment straight to an AWS EC2 instance on every push to `main`.

```text
[ Git Push to Main ] ──▶ [ Lint & Test ] ──▶ [ Docker Build ] ──▶ [ Push to Docker Hub ] ──▶ [ SSH Deploy to EC2 ] ──▶ [ Health Probe HTTP 200 ]
```

#### 📋 Step-by-Step GitHub Actions Setup Guide:

##### Step 1: Fork or Clone Repository to GitHub
Ensure you have the repository on your GitHub account:
```bash
git clone https://github.com/Vaibhavmungal/aws-voting-advanced.git
```

---

##### Step 2: Prepare the Target AWS EC2 Instance
SSH into your AWS EC2 instance and set up the deployment directory:
```bash
# Connect to your EC2 instance
ssh -i <your-key.pem> ubuntu@<your-ec2-ip>

# Ensure Docker is installed and running
sudo apt update && sudo apt install -y docker.io docker-compose-v2
sudo usermod -aG docker ubuntu

# Clone the repository into /opt or home directory
sudo mkdir -p /opt/aws-voting-advanced
sudo chown -R ubuntu:ubuntu /opt/aws-voting-advanced
git clone https://github.com/Vaibhavmungal/aws-voting-advanced.git /opt/aws-voting-advanced
cd /opt/aws-voting-advanced
cp .env.example .env
```

---

##### Step 3: Add GitHub Secrets to Repository
Navigate to your GitHub repository: **Settings > Secrets and variables > Actions > New repository secret**, and add the following 5 secrets:

| Secret Name | Required | Description | Example Value |
|---|---|---|---|
| `DOCKERHUB_USERNAME` | Yes | Your Docker Hub username | `your-dockerhub-user` |
| `DOCKERHUB_TOKEN` | Yes | Docker Hub Personal Access Token (Read & Write) | `dckr_pat_xxxx` |
| `DEPLOY_HOST` | Yes | Public IPv4 or DNS of your AWS EC2 server | `54.210.12.34` |
| `DEPLOY_USER` | Yes | SSH user on your EC2 instance | `ubuntu` |
| `DEPLOY_SSH_KEY` | Yes | Entire private SSH key (`.pem`) used to connect to EC2 | `-----BEGIN RSA PRIVATE KEY-----...` |
| `DEPLOY_PATH` | Optional | Path to project folder on EC2 server | `/opt/aws-voting-advanced` (default) |

> 💡 **Tip for `DEPLOY_SSH_KEY`:** Copy the entire content of your `.pem` key file including `-----BEGIN RSA PRIVATE KEY-----` and `-----END RSA PRIVATE KEY-----`.

---

##### Step 4: Trigger the Pipeline
The workflow triggers automatically whenever code is pushed to the `main` branch:
```bash
git add .
git commit -m "feat: updated election portal"
git push origin main
```
You can also trigger it manually anytime under **GitHub > Actions > VoteSecure CI/CD Pipeline > Run workflow**.

---

##### Step 5: What Happens Automatically
1. **GitHub Runner**: Spawns an Ubuntu runner and checks out the code.
2. **PHP Validation**: Runs `php -l` syntax checking on all PHP scripts.
3. **Docker Buildx**: Builds an optimized production container image.
4. **Publish to Hub**: Pushes images tagged with `:latest` and `:sha-<git_commit>`.
5. **Zero-Downtime Deployment**: SSHs into your AWS EC2 instance, pulls the updated image, runs `scripts/deploy.sh`, and validates the `/health.php` endpoint returns HTTP 200 before completing.

---

##### Step 6: Verify Live Deployment
Test your deployment immediately in the browser or via curl:
```bash
# Verify container is healthy
curl http://<your-ec2-ip>/health.php
# Expected: {"status":"healthy","database":"connected","timestamp":...}
```
- **Live Voting Portal:** `http://<your-ec2-ip>/`
- **Admin Panel:** `http://<your-ec2-ip>/admin/login.php` (User: `Vaibhav`, Pass: `1234`)

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
