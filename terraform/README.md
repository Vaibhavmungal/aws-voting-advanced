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
        │  │ • Port 22 allowed strictly from Bastion SG          │ │
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

## 🎤 Key Presentation Talking Points (For Group Discussion)

When presenting this architecture, highlight these 5 core engineering achievements:

1. **Separation of Concerns & Modularity**:
   - Each module handles a single responsibility (`vpc`, `security`, `bastion`, `compute`, `database`).
   - Root `main.tf` acts as a high-level orchestrator that connects inputs and outputs cleanly.
2. **Chained Zero-Trust Security**:
   - The application server's SSH port (22) is **never exposed to the internet**; it is securely chained to only accept connections from the Bastion Security Group.
   - The database port (3306) only accepts connections from the Application and Bastion Security Groups.
3. **Automated Zero-Touch Bootstrap**:
   - Using cloud-init (`user_data.sh.tpl`), when the EC2 instance launches, it automatically installs Docker, writes the production `.env`, pulls the image from Docker Hub, and starts the container stack without manual SSH needed.
4. **Cost Flexibility**:
   - Supports both **AWS Free-Tier Mode** (`enable_rds = false`, running MySQL in Docker on EC2) and **Enterprise Cloud Mode** (`enable_rds = true` with managed AWS RDS MySQL).
5. **Infrastructure as Code Best Practices**:
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
