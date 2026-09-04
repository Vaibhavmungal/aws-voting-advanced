variable "project_name" {
  description = "Name of the project"
  type        = string
}

variable "environment" {
  description = "Deployment environment"
  type        = string
}

variable "subnet_id" {
  description = "Subnet ID where the App EC2 instance will be deployed"
  type        = string
}

variable "security_group_id" {
  description = "Security Group ID for the App EC2 instance"
  type        = string
}

variable "instance_type" {
  description = "EC2 instance type"
  type        = string
  default     = "t3.micro"
}

variable "root_volume_size" {
  description = "Root disk volume size in GB"
  type        = number
  default     = 20
}

variable "ssh_key_name" {
  description = "AWS Key Pair name"
  type        = string
  default     = ""
}

variable "enable_elastic_ip" {
  description = "Allocate Elastic IP for public instance"
  type        = bool
  default     = true
}

variable "app_in_private_subnet" {
  description = "Whether the App EC2 is in a private subnet"
  type        = bool
  default     = false
}

variable "db_host" {
  description = "Database Host"
  type        = string
  default     = "db"
}

variable "db_port" {
  description = "Database Port"
  type        = number
  default     = 3306
}

variable "db_user" {
  description = "Database User"
  type        = string
  default     = "voting_user"
}

variable "db_pass" {
  description = "Database Password"
  type        = string
  sensitive   = true
}

variable "db_name" {
  description = "Database Name"
  type        = string
  default     = "aws_voting"
}

variable "docker_image" {
  description = "Docker image repository"
  type        = string
  default     = "vaibhavmungal/aws-voting"
}

variable "image_tag" {
  description = "Docker image tag"
  type        = string
  default     = "latest"
}
