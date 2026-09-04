output "public_ip" {
  description = "Public Elastic IP of the Bastion Jump Host"
  value       = aws_eip.this.public_ip
}

output "instance_id" {
  description = "EC2 Instance ID of the Bastion Jump Host"
  value       = aws_instance.this.id
}
