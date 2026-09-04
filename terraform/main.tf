# ==============================================================================
# VoteSecure — Main Infrastructure Orchestration
# ==============================================================================

# 1. VPC & Networking Tier (3-Tier Subnets & NAT)
module "vpc" {
  source             = "./modules/vpc"
  project_name       = var.project_name
  environment        = var.environment
  vpc_cidr           = var.vpc_cidr
  enable_nat_gateway = var.enable_nat_gateway
}

# 2. Security Tier (Firewalls & Least-Privilege Rules)
module "security" {
  source           = "./modules/security"
  project_name     = var.project_name
  environment      = var.environment
  vpc_id           = module.vpc.vpc_id
  allowed_ssh_cidr = var.allowed_ssh_cidr
  enable_bastion   = var.enable_bastion
  enable_rds       = var.enable_rds
}

# 3. Bastion Jump Host (Secure Administrative Access)
module "bastion" {
  count             = var.enable_bastion ? 1 : 0
  source            = "./modules/bastion"
  project_name      = var.project_name
  environment       = var.environment
  subnet_id         = module.vpc.bastion_subnet_id
  security_group_id = module.security.bastion_sg_id
  instance_type     = var.bastion_instance_type
  ssh_key_name      = var.ssh_key_name
}

# 4. Optional AWS RDS MySQL Database (Private Subnets)
module "database" {
  count             = var.enable_rds ? 1 : 0
  source            = "./modules/database"
  project_name      = var.project_name
  environment       = var.environment
  subnet_ids        = module.vpc.private_db_subnet_ids
  security_group_id = module.security.rds_sg_id
  instance_class    = var.rds_instance_class
  db_name           = var.db_name
  db_username       = var.db_username
  db_password       = var.db_password
}

# 5. Compute Tier (VoteSecure App EC2 Server with Automated Bootstrap)
module "compute" {
  source                = "./modules/compute"
  project_name          = var.project_name
  environment           = var.environment
  subnet_id             = var.app_in_private_subnet ? module.vpc.private_app_subnet_1_id : module.vpc.public_subnet_1_id
  security_group_id     = module.security.web_sg_id
  instance_type         = var.instance_type
  root_volume_size      = var.root_volume_size
  ssh_key_name          = var.ssh_key_name
  enable_elastic_ip     = var.enable_elastic_ip
  app_in_private_subnet = var.app_in_private_subnet

  # Auto-wire DB: connects to RDS if enabled, else containerized DB
  db_host = var.enable_rds ? module.database[0].address : "db"
  db_port = var.enable_rds ? module.database[0].port : 3306
  db_user = var.db_username
  db_pass = var.db_password
  db_name = var.db_name

  docker_image = var.docker_image
  image_tag    = var.image_tag
}
