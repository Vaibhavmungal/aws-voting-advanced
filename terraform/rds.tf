# ==============================================================================
# Optional AWS RDS MySQL 8.0 Database (Activated when enable_rds = true)
# ==============================================================================

resource "aws_db_subnet_group" "rds_subnet_group" {
  count       = var.enable_rds ? 1 : 0
  name        = "${var.project_name}-${var.environment}-db-subnets"
  description = "Subnet group across multiple AZs for VoteSecure RDS"
  subnet_ids  = [aws_subnet.private_1.id, aws_subnet.private_2.id]

  tags = {
    Name = "${var.project_name}-${var.environment}-db-subnets"
  }
}

resource "aws_db_parameter_group" "mysql_params" {
  count       = var.enable_rds ? 1 : 0
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

resource "aws_db_instance" "mysql" {
  count                  = var.enable_rds ? 1 : 0
  identifier             = "${var.project_name}-${var.environment}-mysql"
  engine                 = "mysql"
  engine_version         = "8.0"
  instance_class         = var.rds_instance_class
  allocated_storage      = 20
  max_allocated_storage  = 50
  storage_type           = "gp3"
  storage_encrypted      = true

  db_name  = var.db_name
  username = var.db_username
  password = var.db_password
  port     = 3306

  db_subnet_group_name   = aws_db_subnet_group.rds_subnet_group[0].name
  vpc_security_group_ids = [aws_security_group.rds_sg[0].id]
  parameter_group_name   = aws_db_parameter_group.mysql_params[0].name

  publicly_accessible = false
  skip_final_snapshot = true
  deletion_protection = false

  tags = {
    Name = "${var.project_name}-${var.environment}-rds"
  }
}
