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
  description = "CIDR block allowed for SSH access (default 0.0.0.0/0)"
  type        = string
  default     = "0.0.0.0/0"
}

variable "enable_elastic_ip" {
  description = "Allocate a static Elastic IP for the application server"
  type        = bool
  default     = true
}

variable "enable_rds" {
  description = "Set to true to provision a dedicated AWS RDS MySQL instance. If false, MySQL runs containerized on EC2 (Free-Tier friendly)."
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
