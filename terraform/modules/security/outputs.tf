output "web_sg_id" {
  description = "ID of the Web Application security group"
  value       = aws_security_group.web.id
}

output "bastion_sg_id" {
  description = "ID of the Bastion security group"
  value       = var.enable_bastion ? aws_security_group.bastion[0].id : null
}

output "rds_sg_id" {
  description = "ID of the RDS security group"
  value       = var.enable_rds ? aws_security_group.rds[0].id : null
}
