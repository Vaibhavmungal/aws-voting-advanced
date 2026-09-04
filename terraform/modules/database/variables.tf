variable "project_name" {
  description = "Name of the project"
  type        = string
}

variable "environment" {
  description = "Deployment environment"
  type        = string
}

variable "subnet_ids" {
  description = "List of private database subnet IDs across multiple AZs"
  type        = list(string)
}

variable "security_group_id" {
  description = "Security Group ID for RDS MySQL"
  type        = string
}

variable "instance_class" {
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
  sensitive   = true
}
