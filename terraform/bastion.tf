# ==============================================================================
# Bastion Host (Secure Jump Box) in Dedicated Bastion Subnet
# ==============================================================================

# Security Group for Bastion Host
resource "aws_security_group" "bastion_sg" {
  count       = var.enable_bastion ? 1 : 0
  name        = "${var.project_name}-${var.environment}-bastion-sg"
  description = "Allow SSH traffic to Bastion Jump Box from authorized CIDR"
  vpc_id      = aws_vpc.votesecure_vpc.id

  # Inbound SSH strictly from allowed administrator IP range
  ingress {
    description = "Allow SSH from administrator"
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = [var.allowed_ssh_cidr]
  }

  # Outbound traffic (for security patches & SSH forwarding to internal instances)
  egress {
    description = "Allow all outbound traffic"
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name = "${var.project_name}-${var.environment}-bastion-sg"
  }
}

# Bastion EC2 Instance (Lightweight jump box)
resource "aws_instance" "bastion" {
  count                       = var.enable_bastion ? 1 : 0
  ami                         = data.aws_ami.ubuntu.id
  instance_type               = var.bastion_instance_type
  key_name                    = var.ssh_key_name != "" ? var.ssh_key_name : null
  subnet_id                   = aws_subnet.bastion.id
  vpc_security_group_ids      = [aws_security_group.bastion_sg[0].id]
  associate_public_ip_address = true

  root_block_device {
    volume_size           = 10
    volume_type           = "gp3"
    encrypted             = true
    delete_on_termination = true

    tags = {
      Name = "${var.project_name}-${var.environment}-bastion-disk"
    }
  }

  tags = {
    Name = "${var.project_name}-${var.environment}-bastion"
    Role = "JumpHost"
  }
}

# Dedicated Elastic IP for the Bastion Host
resource "aws_eip" "bastion_eip" {
  count    = var.enable_bastion ? 1 : 0
  instance = aws_instance.bastion[0].id
  domain   = "vpc"

  tags = {
    Name = "${var.project_name}-${var.environment}-bastion-eip"
  }
}
