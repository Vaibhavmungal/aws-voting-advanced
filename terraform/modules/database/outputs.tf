output "endpoint" {
  description = "Connection endpoint of the RDS MySQL instance"
  value       = aws_db_instance.this.endpoint
}

output "address" {
  description = "Hostname/Address of the RDS MySQL instance"
  value       = aws_db_instance.this.address
}

output "port" {
  description = "Port of the RDS MySQL database"
  value       = aws_db_instance.this.port
}
