# 🏗️ 3-Tier Multi-Subnet Architecture with Bastion (AWS VoteSecure)

This Terraform module provisions an enterprise-grade **3-Tier AWS Network & Compute Architecture**:
- **Public Subnets**: Ingress, NAT Gateway, Application Load Balancers (`10.0.1.0/24`, `10.0.2.0/24`).
- **Dedicated Bastion Subnet**: Isolated public subnet housing a secure Bastion Jump Box with an Elastic IP (`10.0.3.0/24`).
- **Private Application Subnets**: Private subnets across 2 Availability Zones for VoteSecure EC2 instances (`10.0.11.0/24`, `10.0.12.0/24`).
- **Private Database Subnets**: Isolated subnets across 2 Availability Zones dedicated to AWS RDS MySQL (`10.0.21.0/24`, `10.0.22.0/24`).

---

## 🏛️ Network Topology

```
                                  INTERNET
                                      │
                                      ▼
                        ┌───────────────────────────┐
                        │  Internet Gateway (IGW)   │
                        └─────────────┬─────────────┘
                                      │
      ════════════════════════════════╪════════════════════════════════
      1️⃣ PUBLIC TIER (10.0.1.0/24, 10.0.2.0/24, 10.0.3.0/24)
      ────────────────────────────────────────────────────────────────
        ┌─────────────────────────┐       ┌─────────────────────────┐
        │  Bastion Subnet (AZ-A)  │       │  Public Subnet (AZ-A/B) │
        │  ┌───────────────────┐  │       │                         │
        │  │ Bastion Jump Host │  │       │   [ NAT Gateway ]       │
        │  │ (Elastic IP :22)  │  │       │   (Outbound internet)   │
        │  └─────────┬─────────┘  │       │                         │
        └────────────┼────────────┘       └────────────▲────────────┘
                     │ (Internal SSH only)             │
      ═══════════════╪═════════════════════════════════╪══════════════
      2️⃣ PRIVATE APP TIER (10.0.11.0/24, 10.0.12.0/24) │
      ─────────────────────────────────────────────────┼──────────────
        ┌──────────────────────────────────────────────┴───────────┐
        │  Private App Subnet (AZ-A & AZ-B)                        │
        │  ┌─────────────────────────────────────────────────────┐ │
        │  │ VoteSecure App EC2 (Docker, PHP 8.2 Apache)         │ │
        │  │ • SSH permitted ONLY from Bastion Security Group    │ │
        │  │ • Outbound updates routed through NAT Gateway       │ │
        │  └──────────────────────────┬──────────────────────────┘ │
        └─────────────────────────────┼────────────────────────────┘
                                      │ (MySQL Port 3306)
      ════════════════════════════════╪═══════════════════════════════
      3️⃣ PRIVATE DATABASE TIER (10.0.21.0/24, 10.0.22.0/24)
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

## 🔒 Security Highlights

1. **Zero Public SSH on App Server**: When `enable_bastion = true`, port 22 on the application server accepts connections **exclusively from the Bastion Security Group**.
2. **Database Isolation**: The RDS MySQL instance has zero public routing and lives in dedicated private subnets (`10.0.21.0/24` & `10.0.22.0/24`).
3. **Bastion Jump Host**: Administrator access uses standard SSH ProxyJump (`ssh -J ubuntu@<bastion-ip> ubuntu@<app-private-ip>`).

---

## 🚀 Quickstart Deployment

```bash
# 1. Switch to terraform directory
cd terraform

# 2. Configure variables
cp terraform.tfvars.example terraform.tfvars
```

Configure `terraform.tfvars`:
- `ssh_key_name`: Name of your AWS Key Pair
- `enable_bastion`: `true` (provisions Bastion jump host)
- `enable_rds`: `false` (uses local containerized MySQL) or `true` (provisions AWS RDS)

```bash
# 3. Initialize & Deploy
terraform init
terraform apply
```

---

## 💻 Connecting via Bastion Jump Host

After `terraform apply` finishes, the outputs will show your connection commands:

```bash
# SSH directly to Bastion:
ssh -i ~/.ssh/my-key.pem ubuntu@<BASTION_PUBLIC_IP>

# SSH seamlessly to private App instance through Bastion:
ssh -J ubuntu@<BASTION_PUBLIC_IP> -i ~/.ssh/my-key.pem ubuntu@<APP_PRIVATE_IP>

# Open a secure MySQL tunnel through Bastion to private RDS:
ssh -L 3306:<RDS_ENDPOINT>:3306 -N -i ~/.ssh/my-key.pem ubuntu@<BASTION_PUBLIC_IP>
```

---

## 🧹 Teardown

To delete all provisioned AWS resources:
```bash
terraform destroy
```
