output "vpc_id" {
  description = "ID of the VPC"
  value       = aws_vpc.this.id
}

output "public_subnet_ids" {
  description = "List of public subnet IDs"
  value       = [aws_subnet.public_1.id, aws_subnet.public_2.id]
}

output "public_subnet_1_id" {
  description = "ID of public subnet 1"
  value       = aws_subnet.public_1.id
}

output "bastion_subnet_id" {
  description = "ID of the dedicated Bastion subnet"
  value       = aws_subnet.bastion.id
}

output "private_app_subnet_ids" {
  description = "List of private application subnet IDs"
  value       = [aws_subnet.private_app_1.id, aws_subnet.private_app_2.id]
}

output "private_app_subnet_1_id" {
  description = "ID of private app subnet 1"
  value       = aws_subnet.private_app_1.id
}

output "private_db_subnet_ids" {
  description = "List of private database subnet IDs"
  value       = [aws_subnet.private_db_1.id, aws_subnet.private_db_2.id]
}
