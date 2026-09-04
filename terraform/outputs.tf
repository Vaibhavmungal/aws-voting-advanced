# ==============================================================================
# Root Outputs & Connection Details
# ==============================================================================

locals {
  app_ip = module.compute.public_ip != null && module.compute.public_ip != "" ? module.compute.public_ip : module.compute.private_ip
}

output "app_url" {
  description = "URL to access the VoteSecure application"
  value       = "http://${local.app_ip}"
}

output "app_ip" {
  description = "Public or Private IP address of the application server"
  value       = local.app_ip
}

output "app_private_ip" {
  description = "Internal private IPv4 of the VoteSecure application server"
  value       = module.compute.private_ip
}

# Bastion Jump Host Outputs
output "bastion_public_ip" {
  description = "Static Public IPv4 address of the Bastion Jump Host"
  value       = var.enable_bastion ? module.bastion[0].public_ip : "Bastion is disabled"
}

output "bastion_ssh_command" {
  description = "Command to SSH directly into the Bastion Jump Host"
  value       = (var.enable_bastion && var.ssh_key_name != "") ? "ssh -i ~/.ssh/${var.ssh_key_name}.pem ubuntu@${module.bastion[0].public_ip}" : "Configure ssh_key_name in terraform.tfvars"
}

output "jump_box_ssh_to_app" {
  description = "Secure ProxyJump command to SSH into App server via Bastion"
  value       = (var.enable_bastion && var.ssh_key_name != "") ? "ssh -J ubuntu@${module.bastion[0].public_ip} -i ~/.ssh/${var.ssh_key_name}.pem ubuntu@${module.compute.private_ip}" : "N/A"
}

# Database Outputs
output "rds_endpoint" {
  description = "Connection endpoint of the AWS RDS MySQL database"
  value       = var.enable_rds ? module.database[0].endpoint : "N/A (MySQL is running containerized on EC2 via Docker Compose)"
}

# Network Summary
output "network_summary" {
  description = "IDs of all allocated subnets and VPC"
  value = {
    vpc_id              = module.vpc.vpc_id
    public_subnets      = module.vpc.public_subnet_ids
    bastion_subnet      = module.vpc.bastion_subnet_id
    private_app_subnets = module.vpc.private_app_subnet_ids
    private_db_subnets  = module.vpc.private_db_subnet_ids
  }
}
