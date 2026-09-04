# ==============================================================================
# Outputs & Connection Details
# ==============================================================================

locals {
  public_ip = var.enable_elastic_ip ? aws_eip.web_eip[0].public_ip : aws_instance.web.public_ip
}

output "app_url" {
  description = "Public URL to access the VoteSecure application"
  value       = "http://${local.public_ip}"
}

output "health_check_url" {
  description = "Direct URL to the JSON health check probe"
  value       = "http://${local.public_ip}/health.php"
}

output "ec2_public_ip" {
  description = "Public IPv4 address of the EC2 instance"
  value       = local.public_ip
}

output "ssh_command" {
  description = "Command to SSH into the application server"
  value       = var.ssh_key_name != "" ? "ssh -i ~/.ssh/${var.ssh_key_name}.pem ubuntu@${local.public_ip}" : "ssh ubuntu@${local.public_ip} (configure key pair in variables.tf)"
}

output "rds_endpoint" {
  description = "Connection endpoint of the AWS RDS MySQL database (if enabled)"
  value       = var.enable_rds ? aws_db_instance.mysql[0].endpoint : "N/A (MySQL is running containerized on EC2 via Docker Compose)"
}
