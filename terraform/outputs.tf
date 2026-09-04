# ==============================================================================
# Outputs & Connection Details
# ==============================================================================

locals {
  app_ip = var.app_in_private_subnet ? aws_instance.web.private_ip : (var.enable_elastic_ip ? aws_eip.web_eip[0].public_ip : aws_instance.web.public_ip)
}

output "app_url" {
  description = "URL to access the VoteSecure application"
  value       = "http://${local.app_ip}"
}

output "app_ip" {
  description = "IP address of the application server (Private if app_in_private_subnet=true, Public otherwise)"
  value       = local.app_ip
}

output "app_private_ip" {
  description = "Internal private IPv4 of the VoteSecure application server"
  value       = aws_instance.web.private_ip
}

# Bastion Jump Host Outputs
output "bastion_public_ip" {
  description = "Static Public IPv4 address of the Bastion Jump Host"
  value       = var.enable_bastion ? aws_eip.bastion_eip[0].public_ip : "Bastion is disabled"
}

output "bastion_ssh_command" {
  description = "Command to SSH directly into the Bastion Jump Host"
  value       = (var.enable_bastion && var.ssh_key_name != "") ? "ssh -i ~/.ssh/${var.ssh_key_name}.pem ubuntu@${aws_eip.bastion_eip[0].public_ip}" : "Configure ssh_key_name in terraform.tfvars"
}

output "jump_box_ssh_to_app" {
  description = "Secure ProxyJump command to SSH into App server via Bastion"
  value       = (var.enable_bastion && var.ssh_key_name != "") ? "ssh -J ubuntu@${aws_eip.bastion_eip[0].public_ip} -i ~/.ssh/${var.ssh_key_name}.pem ubuntu@${aws_instance.web.private_ip}" : "N/A"
}

# Database Outputs
output "rds_endpoint" {
  description = "Connection endpoint of the AWS RDS MySQL database"
  value       = var.enable_rds ? aws_db_instance.mysql[0].endpoint : "N/A (MySQL is running containerized on EC2)"
}

# Subnet IDs Overview
output "subnets_summary" {
  description = "IDs of all allocated subnets by tier"
  value = {
    public_subnets      = [aws_subnet.public_1.id, aws_subnet.public_2.id]
    bastion_subnet      = aws_subnet.bastion.id
    private_app_subnets = [aws_subnet.private_app_1.id, aws_subnet.private_app_2.id]
    private_db_subnets  = [aws_subnet.private_db_1.id, aws_subnet.private_db_2.id]
  }
}
