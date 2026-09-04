# ==============================================================================
# AWS EC2 Compute Instance & Elastic IP
# ==============================================================================

# Lookup latest official Ubuntu 22.04 LTS AMI
data "aws_ami" "ubuntu" {
  most_recent = true
  owners      = ["099720109477"] # Canonical

  filter {
    name   = "name"
    values = ["ubuntu/images/hvm-ssd/ubuntu-jammy-22.04-amd64-server-*"]
  }

  filter {
    name   = "virtualization-type"
    values = ["hvm"]
  }
}

# Primary Application Server
resource "aws_instance" "web" {
  ami                         = data.aws_ami.ubuntu.id
  instance_type               = var.instance_type
  key_name                    = var.ssh_key_name != "" ? var.ssh_key_name : null
  subnet_id                   = aws_subnet.public_1.id
  vpc_security_group_ids      = [aws_security_group.web_sg.id]
  associate_public_ip_address = true

  root_block_device {
    volume_size           = var.root_volume_size
    volume_type           = "gp3"
    encrypted             = true
    delete_on_termination = true

    tags = {
      Name = "${var.project_name}-${var.environment}-root-volume"
    }
  }

  # Cloud-Init automated bootstrap script
  user_data = templatefile("${path.module}/scripts/user_data.sh.tpl", {
    db_host      = var.enable_rds ? aws_db_instance.mysql[0].address : "db"
    db_port      = var.enable_rds ? aws_db_instance.mysql[0].port : 3306
    db_user      = var.db_username
    db_pass      = var.db_password
    db_name      = var.db_name
    docker_image = var.docker_image
    image_tag    = var.image_tag
  })

  user_data_replace_on_change = true

  tags = {
    Name = "${var.project_name}-${var.environment}-app"
  }
}

# Optional Elastic IP for static public IPv4
resource "aws_eip" "web_eip" {
  count    = var.enable_elastic_ip ? 1 : 0
  instance = aws_instance.web.id
  domain   = "vpc"

  tags = {
    Name = "${var.project_name}-${var.environment}-eip"
  }
}
