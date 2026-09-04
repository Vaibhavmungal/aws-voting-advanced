# ==============================================================================
# Input Variables
# ==============================================================================

variable "aws_region" {
  description = "AWS region for deployment"
  type        = string
  default     = "ap-south-1"
}

variable "project_name" {
  description = "Name of the project used in resource tags and identifiers"
  type        = string
  default     = "votesecure"
}

variable "environment" {
  description = "Deployment environment (prod, staging, dev)"
  type        = string
  default     = "prod"
}

variable "vpc_cidr" {
  description = "CIDR block for the Virtual Private Cloud"
  type        = string
  default     = "10.0.0.0/16"
}

# ------------------------------------------------------------------------------
# 💻 Compute & Bastion Configuration
# ------------------------------------------------------------------------------

variable "instance_type" {
  description = "EC2 instance type (Free Tier eligible: t3.micro or t2.micro)"
  type        = string
  default     = "t3.micro"
}

variable "root_volume_size" {
  description = "Size of the root EBS volume in GB (gp3)"
  type        = number
  default     = 20
}

variable "ssh_key_name" {
  description = "Existing AWS Key Pair name for SSH access (optional)"
  type        = string
  default     = ""
}

variable "allowed_ssh_cidr" {
  description = "CIDR block allowed for SSH access to Bastion (default 0.0.0.0/0)"
  type        = string
  default     = "0.0.0.0/0"
}

variable "enable_bastion" {
  description = "Deploy a dedicated Bastion (Jump Host) in the Bastion subnet for secure SSH"
  type        = bool
  default     = true
}

variable "bastion_instance_type" {
  description = "EC2 instance type for Bastion Host"
  type        = string
  default     = "t3.nano"
}

variable "enable_elastic_ip" {
  description = "Allocate a static Elastic IP for the application server"
  type        = bool
  default     = true
}

variable "enable_nat_gateway" {
  description = "Provision AWS Managed NAT Gateway for private subnets (costs ~$0.045/hr). Set to false to avoid NAT charges."
  type        = bool
  default     = false
}

variable "app_in_private_subnet" {
  description = "Place App EC2 in private app subnet (requires enable_nat_gateway = true for outbound traffic)"
  type        = bool
  default     = false
}

# ------------------------------------------------------------------------------
# 🗄️ Database Configuration
# ------------------------------------------------------------------------------

variable "enable_rds" {
  description = "Set to true to provision a dedicated AWS RDS MySQL instance in private DB subnets. If false, MySQL runs containerized on EC2."
  type        = bool
  default     = false
}

variable "rds_instance_class" {
  description = "Instance class for RDS database"
  type        = string
  default     = "db.t3.micro"
}

variable "db_name" {
  description = "Database name"
  type        = string
  default     = "aws_voting"
}

variable "db_username" {
  description = "Database master username"
  type        = string
  default     = "voting_user"
}

variable "db_password" {
  description = "Database master password"
  type        = string
  default     = "VotingSecurePass2026!"
  sensitive   = true
}

# ------------------------------------------------------------------------------
# 🐳 Container Image
# ------------------------------------------------------------------------------

variable "docker_image" {
  description = "Docker Hub repository image to run"
  type        = string
  default     = "vaibhavmungal/aws-voting"
}

variable "image_tag" {
  description = "Docker image tag"
  type        = string
  default     = "latest"
}
