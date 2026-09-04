variable "project_name" {
  description = "Name of the project"
  type        = string
}

variable "environment" {
  description = "Deployment environment (prod, staging, dev)"
  type        = string
}

variable "vpc_id" {
  description = "VPC ID where security groups will be created"
  type        = string
}

variable "allowed_ssh_cidr" {
  description = "CIDR block allowed to SSH into Bastion"
  type        = string
  default     = "0.0.0.0/0"
}

variable "enable_bastion" {
  description = "Whether bastion host is enabled (enforces SSH chaining)"
  type        = bool
  default     = true
}

variable "enable_rds" {
  description = "Whether RDS database is enabled"
  type        = bool
  default     = false
}
