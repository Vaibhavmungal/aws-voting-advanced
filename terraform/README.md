# 🏛️ Production Modular Terraform Architecture (AWS VoteSecure)

> **Presentation & Architecture Guide:** Built specifically for group reviews, architecture evaluations, and production deployments on AWS.

---

## 📂 Project Structure & Modularity

The Terraform codebase is structured into self-contained, decoupled, and reusable **modules**:

```text
terraform/
├── provider.tf                   # Provider definition & default tags
├── main.tf                       # High-level module orchestrator (~50 lines)
├── variables.tf                  # Global input parameters
├── outputs.tf                    # Aggregated connection details & endpoints
├── terraform.tfvars.example      # Example variable values for easy setup
│
└── modules/
    ├── vpc/                      # 3-Tier Multi-AZ VPC, Subnets & Routing
    │   ├── main.tf
    │   ├── variables.tf
    │   └── outputs.tf
    │
    ├── security/                 # Chained Least-Privilege Security Groups
    │   ├── main.tf
    │   ├── variables.tf
    │   └── outputs.tf
    │
    ├── bastion/                  # Dedicated Jump Host with Elastic IP
    │   ├── main.tf
    │   ├── variables.tf
    │   └── outputs.tf
    │
    ├── compute/                  # VoteSecure EC2 App Server + Cloud-Init Bootstrap
    │   ├── main.tf
    │   ├── variables.tf
    │   ├── outputs.tf
    │   └── scripts/
    │       └── user_data.sh.tpl  # Zero-touch Docker startup script
    │
    └── database/                 # AWS RDS MySQL 8.0 with Multi-AZ Subnet Group
        ├── main.tf
        ├── variables.tf
        └── outputs.tf
```

---

## 🏛️ 3-Tier Infrastructure Diagram

```
                                  INTERNET
                                      │
                                      ▼
                        ┌───────────────────────────┐
                        │  Internet Gateway (IGW)   │
                        └─────────────┬─────────────┘
                                      │
      ════════════════════════════════╪════════════════════════════════
      1️⃣ PUBLIC / INGRESS TIER (Subnets: 10.0.1.0/24, 10.0.2.0/24, 10.0.3.0/24)
      ────────────────────────────────────────────────────────────────
        ┌─────────────────────────┐       ┌─────────────────────────┐
        │  Bastion Subnet (AZ-A)  │       │  Public Subnets (AZ-A/B)│
        │  ┌───────────────────┐  │       │                         │
        │  │ Bastion Jump Host │  │       │   [ NAT Gateway ]       │
        │  │ (Elastic IP :22)  │  │       │   (Outbound internet)   │
        │  └─────────┬─────────┘  │       │                         │
        └────────────┼────────────┘       └────────────▲────────────┘
                     │ (Internal SSH only)             │
      ═══════════════╪═════════════════════════════════╪══════════════
      2️⃣ PRIVATE APPLICATION TIER (Subnets: 10.0.11.0/24, 10.0.12.0/24)
      ─────────────────────────────────────────────────┼──────────────
        ┌──────────────────────────────────────────────┴───────────┐
        │  Private App Subnet (AZ-A & AZ-B)                        │
        │  ┌─────────────────────────────────────────────────────┐ │
        │  │ VoteSecure App EC2 (Docker Engine, PHP 8.2 Apache)  │ │
        │  │ • Port 80 (HTTP) & 443 (HTTPS) - Web Traffic        │ │
        │  │ • Port 8080 (TCP) - Jenkins CI/CD Dashboard         │ │
        │  │ • Port 8085 (TCP) - VoteSecure App Alternate        │ │
        │  │ • Port 22 (SSH) allowed strictly from Bastion SG    │ │
        │  │ • Automated cloud-init bootstrap on launch          │ │
        │  └──────────────────────────┬──────────────────────────┘ │
        └─────────────────────────────┼────────────────────────────┘
                                      │ (MySQL Port 3306)
      ════════════════════════════════╪═══════════════════════════════
      3️⃣ PRIVATE DATABASE TIER (Subnets: 10.0.21.0/24, 10.0.22.0/24)
      ────────────────────────────────────────────────────────────────
        ┌──────────────────────────────────────────────────────────┐
        │  Private DB Subnets (AZ-A & AZ-B)                        │
        │  ┌─────────────────────────────────────────────────────┐ │
        │  │ AWS RDS MySQL 8.0 (Multi-AZ DB Subnet Group)        │ │
        │  │ • No Internet access                                │ │
        │  │ • Port 3306 allowed strictly from App & Bastion SGs │ │
        │  └─────────────────────────────────────────────────────┘ │
        └──────────────────────────────────────────────────────────┘
```

---

## 🔒 Chained Security Groups & Port Allocation Matrix

| Security Group | Inbound Port | Protocol | Source / CIDR | Purpose & Traffic Description |
|---|---|---|---|---|
| **Bastion Host SG** (`bastion-sg`) | `22` | TCP | `var.allowed_ssh_cidr` (Admin IP) | Secure administrative SSH jump box entrypoint |
| **Web / App SG** (`web-sg`) | `80` | TCP | `0.0.0.0/0` (Internet) | Production public web traffic (VoteSecure) |
| **Web / App SG** (`web-sg`) | `443` | TCP | `0.0.0.0/0` (Internet) | Production SSL/TLS encrypted traffic |
| **Web / App SG** (`web-sg`) | `8080` | TCP | `0.0.0.0/0` (Internet) | **Jenkins CI/CD Web Dashboard** (Strictly dedicated to build pipelines) |
| **Web / App SG** (`web-sg`) | `8085` | TCP | `0.0.0.0/0` (Internet) | **VoteSecure App Alternate** (Local testing & Docker Compose runtime) |
| **Web / App SG** (`web-sg`) | `22` | TCP | `bastion-sg` (Chained) | **Zero-Trust SSH**: Web host rejects direct internet SSH connections |
| **RDS MySQL SG** (`rds-sg`) | `3306` | TCP | `web-sg`, `bastion-sg` | **Air-gapped Database**: Completely isolated from public internet |

> 💡 **Port Conflict Prevention:** Port `8080` is reserved strictly for Jenkins CI/CD. VoteSecure web traffic is routed on port `80` in production and port `8085` for container/local environments.

---

## 🎤 Key Presentation Talking Points (For Group Discussion)

When presenting this architecture, highlight these core engineering achievements:

1. **Separation of Concerns & Modularity**:
   - Each module handles a single responsibility (`vpc`, `security`, `bastion`, `compute`, `database`).
   - Root `main.tf` acts as a high-level orchestrator that connects inputs and outputs cleanly.
2. **Chained Zero-Trust Security**:
   - The application server's SSH port (22) is **never exposed to the internet**; it is securely chained to only accept connections from the Bastion Security Group.
   - The database port (3306) only accepts connections from the Application and Bastion Security Groups.
3. **Dedicated Port Governance**:
   - Resolves all port collisions by reserving port `8080` strictly for Jenkins CI/CD automation, while allocating port `80` (HTTP) and port `8085` for the VoteSecure application.
4. **Automated Zero-Touch Bootstrap**:
   - Using cloud-init (`user_data.sh.tpl`), when the EC2 instance launches, it automatically installs Docker, writes the production `.env`, pulls the image from Docker Hub, and starts the container stack without manual SSH needed.
5. **Cost Flexibility**:
   - Supports both **AWS Free-Tier Mode** (`enable_rds = false`, running MySQL in Docker on EC2) and **Enterprise Cloud Mode** (`enable_rds = true` with managed AWS RDS MySQL).
6. **Infrastructure as Code Best Practices**:
   - Strict version locking in `provider.tf`, unified resource tagging (`Project`, `Environment`, `ManagedBy`), and clean outputs for instant access.

---

## 🚀 Presentation Demo Commands

### 1. Initialize Modules & Providers
```bash
terraform init
```
*Explains: Terraform downloads AWS provider and links all 5 internal modules.*

### 2. Validate & Inspect Plan
```bash
terraform validate
terraform plan
```
*Explains: Dry-run showing all AWS resources that will be provisioned.*

### 3. Deploy Stack
```bash
terraform apply
```
*Explains: Provisions VPC, Subnets, Security Groups, Bastion, and App Server.*

### 4. Connect via Jump Box
```bash
# Connect to Bastion:
ssh -i ~/.ssh/my-key.pem ubuntu@<BASTION_PUBLIC_IP>

# ProxyJump through Bastion to App Server:
ssh -J ubuntu@<BASTION_PUBLIC_IP> -i ~/.ssh/my-key.pem ubuntu@<APP_PRIVATE_IP>
```

### 5. Cleanup / Teardown
```bash
terraform destroy
```
