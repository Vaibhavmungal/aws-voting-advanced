# ==============================================================================
# Compute Module — VoteSecure EC2 App Server
# ==============================================================================

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

resource "aws_instance" "app" {
  ami                         = data.aws_ami.ubuntu.id
  instance_type               = var.instance_type
  key_name                    = var.ssh_key_name != "" ? var.ssh_key_name : null
  subnet_id                   = var.subnet_id
  vpc_security_group_ids      = [var.security_group_id]
  associate_public_ip_address = var.app_in_private_subnet ? false : true

  root_block_device {
    volume_size           = var.root_volume_size
    volume_type           = "gp3"
    encrypted             = true
    delete_on_termination = true

    tags = {
      Name = "${var.project_name}-${var.environment}-app-disk"
    }
  }

  user_data = templatefile("${path.module}/scripts/user_data.sh.tpl", {
    db_host      = var.db_host
    db_port      = var.db_port
    db_user      = var.db_user
    db_pass      = var.db_pass
    db_name      = var.db_name
    docker_image = var.docker_image
    image_tag    = var.image_tag
  })

  user_data_replace_on_change = true

  tags = {
    Name = "${var.project_name}-${var.environment}-app"
    Tier = var.app_in_private_subnet ? "PrivateApp" : "PublicApp"
  }
}

resource "aws_eip" "app_eip" {
  count    = (var.enable_elastic_ip && !var.app_in_private_subnet) ? 1 : 0
  instance = aws_instance.app.id
  domain   = "vpc"

  tags = {
    Name = "${var.project_name}-${var.environment}-app-eip"
  }
}
