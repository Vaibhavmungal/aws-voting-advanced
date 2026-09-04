variable "project_name" {
  description = "Name of the project"
  type        = string
}

variable "environment" {
  description = "Deployment environment"
  type        = string
}

variable "subnet_id" {
  description = "Subnet ID where the Bastion host will be deployed"
  type        = string
}

variable "security_group_id" {
  description = "Security Group ID for the Bastion host"
  type        = string
}

variable "instance_type" {
  description = "EC2 instance type for Bastion"
  type        = string
  default     = "t3.nano"
}

variable "ssh_key_name" {
  description = "AWS Key Pair name for SSH"
  type        = string
  default     = ""
}
