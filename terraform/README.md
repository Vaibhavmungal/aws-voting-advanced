# 🏗️ Terraform Infrastructure as Code (AWS VoteSecure)

This directory contains production-ready **Terraform** configurations to provision the complete AWS cloud infrastructure for **VoteSecure** with a single command.

---

## 🏛️ Architecture Overview

```
                          AWS CLOUD
┌─────────────────────────────────────────────────────────────┐
│ Custom VPC (10.0.0.0/16) + Internet Gateway                │
│                                                             │
│   ┌─────────────────────────────────────────────────────┐   │
│   │ Public Subnet (10.0.1.0/24)                         │   │
│   │                                                     │   │
│   │   [ Elastic IP ]                                    │   │
│   │         │                                           │   │
│   │   ┌─────▼───────────────────────────────────────┐   │   │
│   │   │ EC2 Instance (t3.micro, Ubuntu 22.04)       │   │   │
│   │   │   • Docker Engine + Docker Compose v2       │   │   │
│   │   │   • VoteSecure App Container (:80)          │   │   │
│   │   │   • Local MySQL Container (if enable_rds=F) │   │   │
│   │   └─────────────────────────────────────────────┘   │   │
│   └─────────────────────────────────────────────────────┘   │
│                                                             │
│   ┌─────────────────────────────────────────────────────┐   │
│   │ Private Subnet (Optional, if enable_rds = true)     │   │
│   │                                                     │   │
│   │   ┌─────────────────────────────────────────────┐   │   │
│   │   │ AWS RDS MySQL 8.0 (db.t3.micro)             │   │   │
│   │   │ (Accessible only from EC2 Web SG)           │   │   │
│   │   └─────────────────────────────────────────────┘   │   │
│   └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

---

## 📋 Prerequisites

1. **Terraform CLI** installed (`>= 1.5.0`):
   ```bash
   terraform -version
   ```
2. **AWS CLI** installed and configured with appropriate IAM permissions (VPC, EC2, RDS):
   ```bash
   aws configure
   ```
3. *(Optional)* An AWS Key Pair in your chosen region if you want to SSH into the instance.

---

## 🚀 Quickstart Deployment

### 1. Navigate to the terraform directory:
```bash
cd terraform
```

### 2. Configure variables:
```bash
cp terraform.tfvars.example terraform.tfvars
```
Open `terraform.tfvars` and configure:
- `aws_region`: Target region (e.g. `ap-south-1` or `us-east-1`).
- `ssh_key_name`: Name of your AWS Key Pair (leave blank if SSH key is not needed).
- `enable_rds`: `false` (default: runs MySQL on EC2 at no extra DB cost) or `true` (provisions AWS RDS).

### 3. Initialize Terraform:
```bash
terraform init
```

### 4. Review the Execution Plan:
```bash
terraform plan
```

### 5. Apply & Provision Infrastructure:
```bash
terraform apply
```
Type `yes` when prompted. Terraform will provision the VPC, Subnets, Security Groups, and EC2 instance.

---

## 📤 Outputs

After `terraform apply` finishes, you will see output values:

```text
Outputs:

app_url          = "http://13.206.147.173"
health_check_url = "http://13.206.147.173/health.php"
ec2_public_ip    = "13.206.147.173"
ssh_command      = "ssh -i ~/.ssh/my-key.pem ubuntu@13.206.147.173"
rds_endpoint     = "N/A (MySQL is running containerized on EC2 via Docker Compose)"
```

Open `app_url` in your browser. The application is live!

---

## 🧹 Teardown / Cleanup

To delete all AWS cloud resources and avoid ongoing AWS charges:

```bash
terraform destroy
```
Type `yes` to confirm destruction.
