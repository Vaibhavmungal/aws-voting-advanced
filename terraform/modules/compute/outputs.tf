output "public_ip" {
  description = "Public IP of the Application instance (if public)"
  value       = (var.enable_elastic_ip && !var.app_in_private_subnet) ? aws_eip.app_eip[0].public_ip : aws_instance.app.public_ip
}

output "private_ip" {
  description = "Private IP of the Application instance"
  value       = aws_instance.app.private_ip
}

output "instance_id" {
  description = "Instance ID of the Application server"
  value       = aws_instance.app.id
}
