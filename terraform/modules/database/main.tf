# ==============================================================================
# Database Module — Isolated AWS RDS MySQL 8.0
# ==============================================================================

resource "aws_db_subnet_group" "this" {
  name        = "${var.project_name}-${var.environment}-db-subnets"
  description = "Subnet group across private DB subnets"
  subnet_ids  = var.subnet_ids

  tags = {
    Name = "${var.project_name}-${var.environment}-db-subnets"
  }
}

resource "aws_db_parameter_group" "mysql8" {
  name        = "${var.project_name}-${var.environment}-mysql8-params"
  family      = "mysql8.0"
  description = "Custom parameters for UTF-8 compatibility"

  parameter {
    name  = "character_set_server"
    value = "utf8mb4"
  }

  parameter {
    name  = "collation_server"
    value = "utf8mb4_general_ci"
  }

  tags = {
    Name = "${var.project_name}-${var.environment}-mysql-params"
  }
}

resource "aws_db_instance" "this" {
  identifier            = "${var.project_name}-${var.environment}-mysql"
  engine                = "mysql"
  engine_version        = "8.0"
  instance_class        = var.instance_class
  allocated_storage     = 20
  max_allocated_storage = 50
  storage_type          = "gp3"
  storage_encrypted     = true

  db_name  = var.db_name
  username = var.db_username
  password = var.db_password
  port     = 3306

  db_subnet_group_name   = aws_db_subnet_group.this.name
  vpc_security_group_ids = [var.security_group_id]
  parameter_group_name   = aws_db_parameter_group.mysql8.name

  publicly_accessible = false
  skip_final_snapshot = true
  deletion_protection = false

  tags = {
    Name = "${var.project_name}-${var.environment}-rds"
  }
}
