# ==============================================================================
# Security Groups & Firewall Rules
# ==============================================================================

# Security Group for Application Instance
resource "aws_security_group" "web_sg" {
  name        = "${var.project_name}-${var.environment}-web-sg"
  description = "Security group for VoteSecure Web Application"
  vpc_id      = aws_vpc.votesecure_vpc.id

  # HTTP
  ingress {
    description = "Allow HTTP from anywhere"
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  # HTTPS
  ingress {
    description = "Allow HTTPS from anywhere"
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  # Application Port (8080)
  ingress {
    description = "Allow custom app port 8080"
    from_port   = 8080
    to_port     = 8080
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  # SSH: Securely chained via Bastion if enabled, else fallback to allowed CIDR
  ingress {
    description     = "Allow SSH strictly from Bastion Jump Host"
    from_port       = 22
    to_port         = 22
    protocol        = "tcp"
    security_groups = var.enable_bastion ? [aws_security_group.bastion_sg[0].id] : null
    cidr_blocks     = var.enable_bastion ? null : [var.allowed_ssh_cidr]
  }

  # All Outbound
  egress {
    description = "Allow all outbound traffic"
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name = "${var.project_name}-${var.environment}-web-sg"
  }
}

# Security Group for RDS MySQL Database (Strictly Isolated)
resource "aws_security_group" "rds_sg" {
  count       = var.enable_rds ? 1 : 0
  name        = "${var.project_name}-${var.environment}-rds-sg"
  description = "Allow MySQL traffic strictly from App and Bastion security groups"
  vpc_id      = aws_vpc.votesecure_vpc.id

  # MySQL from App Server
  ingress {
    description     = "Allow MySQL access from Web App SG"
    from_port       = 3306
    to_port         = 3306
    protocol        = "tcp"
    security_groups = [aws_security_group.web_sg.id]
  }

  # MySQL from Bastion Jump Host (for DB administration/tunneling)
  dynamic "ingress" {
    for_each = var.enable_bastion ? [1] : []
    content {
      description     = "Allow MySQL tunnel from Bastion Jump Host"
      from_port       = 3306
      to_port         = 3306
      protocol        = "tcp"
      security_groups = [aws_security_group.bastion_sg[0].id]
    }
  }

  egress {
    description = "Allow all outbound traffic"
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name = "${var.project_name}-${var.environment}-rds-sg"
  }
}
