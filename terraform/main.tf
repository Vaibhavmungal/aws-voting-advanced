# ==============================================================================
# VoteSecure — 3-Tier Multi-Subnet AWS Architecture
# ==============================================================================

terraform {
  required_version = ">= 1.5.0"
  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 5.0"
    }
  }
}

provider "aws" {
  region = var.aws_region

  default_tags {
    tags = {
      Project     = var.project_name
      Environment = var.environment
      ManagedBy   = "Terraform"
    }
  }
}

data "aws_availability_zones" "available" {
  state = "available"
}

# ------------------------------------------------------------------------------
# 🌐 VPC & Internet Gateway
# ------------------------------------------------------------------------------

resource "aws_vpc" "votesecure_vpc" {
  cidr_block           = var.vpc_cidr
  enable_dns_hostnames = true
  enable_dns_support   = true

  tags = {
    Name = "${var.project_name}-${var.environment}-vpc"
  }
}

resource "aws_internet_gateway" "igw" {
  vpc_id = aws_vpc.votesecure_vpc.id

  tags = {
    Name = "${var.project_name}-${var.environment}-igw"
  }
}

# ------------------------------------------------------------------------------
# 1️⃣ TIER 1: PUBLIC SUBNETS (Web / ALB / Ingress)
# ------------------------------------------------------------------------------

resource "aws_subnet" "public_1" {
  vpc_id                  = aws_vpc.votesecure_vpc.id
  cidr_block              = cidrsubnet(var.vpc_cidr, 8, 1) # 10.0.1.0/24
  availability_zone       = data.aws_availability_zones.available.names[0]
  map_public_ip_on_launch = true

  tags = {
    Name = "${var.project_name}-${var.environment}-public-1"
    Tier = "Public"
  }
}

resource "aws_subnet" "public_2" {
  vpc_id                  = aws_vpc.votesecure_vpc.id
  cidr_block              = cidrsubnet(var.vpc_cidr, 8, 2) # 10.0.2.0/24
  availability_zone       = data.aws_availability_zones.available.names[1]
  map_public_ip_on_launch = true

  tags = {
    Name = "${var.project_name}-${var.environment}-public-2"
    Tier = "Public"
  }
}

# Dedicated Bastion Jump Host Subnet
resource "aws_subnet" "bastion" {
  vpc_id                  = aws_vpc.votesecure_vpc.id
  cidr_block              = cidrsubnet(var.vpc_cidr, 8, 3) # 10.0.3.0/24
  availability_zone       = data.aws_availability_zones.available.names[0]
  map_public_ip_on_launch = true

  tags = {
    Name = "${var.project_name}-${var.environment}-bastion-subnet"
    Tier = "Bastion"
  }
}

# ------------------------------------------------------------------------------
# 2️⃣ TIER 2: PRIVATE APPLICATION SUBNETS (App EC2 Instances)
# ------------------------------------------------------------------------------

resource "aws_subnet" "private_app_1" {
  vpc_id                  = aws_vpc.votesecure_vpc.id
  cidr_block              = cidrsubnet(var.vpc_cidr, 8, 11) # 10.0.11.0/24
  availability_zone       = data.aws_availability_zones.available.names[0]
  map_public_ip_on_launch = false

  tags = {
    Name = "${var.project_name}-${var.environment}-private-app-1"
    Tier = "PrivateApp"
  }
}

resource "aws_subnet" "private_app_2" {
  vpc_id                  = aws_vpc.votesecure_vpc.id
  cidr_block              = cidrsubnet(var.vpc_cidr, 8, 12) # 10.0.12.0/24
  availability_zone       = data.aws_availability_zones.available.names[1]
  map_public_ip_on_launch = false

  tags = {
    Name = "${var.project_name}-${var.environment}-private-app-2"
    Tier = "PrivateApp"
  }
}

# ------------------------------------------------------------------------------
# 3️⃣ TIER 3: PRIVATE DATABASE SUBNETS (Strictly Isolated RDS MySQL)
# ------------------------------------------------------------------------------

resource "aws_subnet" "private_db_1" {
  vpc_id                  = aws_vpc.votesecure_vpc.id
  cidr_block              = cidrsubnet(var.vpc_cidr, 8, 21) # 10.0.21.0/24
  availability_zone       = data.aws_availability_zones.available.names[0]
  map_public_ip_on_launch = false

  tags = {
    Name = "${var.project_name}-${var.environment}-private-db-1"
    Tier = "PrivateDatabase"
  }
}

resource "aws_subnet" "private_db_2" {
  vpc_id                  = aws_vpc.votesecure_vpc.id
  cidr_block              = cidrsubnet(var.vpc_cidr, 8, 22) # 10.0.22.0/24
  availability_zone       = data.aws_availability_zones.available.names[1]
  map_public_ip_on_launch = false

  tags = {
    Name = "${var.project_name}-${var.environment}-private-db-2"
    Tier = "PrivateDatabase"
  }
}

# ------------------------------------------------------------------------------
# 🚦 Route Tables & Associations
# ------------------------------------------------------------------------------

# Public Route Table (Routes out to Internet Gateway)
resource "aws_route_table" "public_rt" {
  vpc_id = aws_vpc.votesecure_vpc.id

  route {
    cidr_block = "0.0.0.0/0"
    gateway_id = aws_internet_gateway.igw.id
  }

  tags = {
    Name = "${var.project_name}-${var.environment}-public-rt"
  }
}

resource "aws_route_table_association" "public_1" {
  subnet_id      = aws_subnet.public_1.id
  route_table_id = aws_route_table.public_rt.id
}

resource "aws_route_table_association" "public_2" {
  subnet_id      = aws_subnet.public_2.id
  route_table_id = aws_route_table.public_rt.id
}

resource "aws_route_table_association" "bastion" {
  subnet_id      = aws_subnet.bastion.id
  route_table_id = aws_route_table.public_rt.id
}

# Optional NAT Gateway for Private Subnets
resource "aws_eip" "nat_eip" {
  count  = var.enable_nat_gateway ? 1 : 0
  domain = "vpc"

  tags = {
    Name = "${var.project_name}-${var.environment}-nat-eip"
  }
}

resource "aws_nat_gateway" "nat" {
  count         = var.enable_nat_gateway ? 1 : 0
  allocation_id = aws_eip.nat_eip[0].id
  subnet_id     = aws_subnet.public_1.id

  tags = {
    Name = "${var.project_name}-${var.environment}-nat-gw"
  }

  depends_on = [aws_internet_gateway.igw]
}

# Private Route Table
resource "aws_route_table" "private_rt" {
  vpc_id = aws_vpc.votesecure_vpc.id

  dynamic "route" {
    for_each = var.enable_nat_gateway ? [1] : []
    content {
      cidr_block     = "0.0.0.0/0"
      nat_gateway_id = aws_nat_gateway.nat[0].id
    }
  }

  tags = {
    Name = "${var.project_name}-${var.environment}-private-rt"
  }
}

resource "aws_route_table_association" "private_app_1" {
  subnet_id      = aws_subnet.private_app_1.id
  route_table_id = aws_route_table.private_rt.id
}

resource "aws_route_table_association" "private_app_2" {
  subnet_id      = aws_subnet.private_app_2.id
  route_table_id = aws_route_table.private_rt.id
}

resource "aws_route_table_association" "private_db_1" {
  subnet_id      = aws_subnet.private_db_1.id
  route_table_id = aws_route_table.private_rt.id
}

resource "aws_route_table_association" "private_db_2" {
  subnet_id      = aws_subnet.private_db_2.id
  route_table_id = aws_route_table.private_rt.id
}
