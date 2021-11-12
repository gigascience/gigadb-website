locals {
  tstamp = formatdate("YYYYMMDDhhmmss", timestamp())
}

module "security_group" {
  source  = "terraform-aws-modules/security-group/aws"
  version = "~> 4"

  name        = "rds_sg_${var.deployment_target}_${var.owner}"
  description = "Security group for GigaDB RDS"
  vpc_id      = var.vpc_id

  ingress_with_cidr_blocks = [
    {
      description = "PostgreSQL access only for internal VPC clients, i.e. dockerhost and bastion servers"
      from_port   = 5432
      to_port     = 5432
      protocol    = "tcp"
      cidr_blocks = "10.99.0.0/18"
    }
  ]
}

module "db" {
  source = "terraform-aws-modules/rds/aws"
  identifier = "rds-server-${var.deployment_target}-${var.owner}"

  snapshot_identifier = var.snapshot_identifier
  restore_to_point_in_time = var.restore_to_point_in_time

  name                   = var.gigadb_db_database
  username               = var.gigadb_db_user
  password               = var.gigadb_db_password
  port                   = 5432

  subnet_ids             = var.rds_subnet_ids
  vpc_security_group_ids = [module.security_group.security_group_id]

  create_db_option_group    = false
  create_db_parameter_group = false
  engine                    = "postgres"
  engine_version            = "11.13"
  family                    = "postgres11"  # DB parameter group
  major_engine_version      = "11"          # DB option group
  instance_class            = "db.t3.micro"
  allocated_storage         = 8
  deletion_protection       = false
  maintenance_window        = "Mon:00:00-Mon:03:00"
  backup_window             = "03:00-06:00"  # UTC time
  backup_retention_period   = 5  # days
  skip_final_snapshot       = false  # Create final snapshot
  final_snapshot_identifier = "snapshot-final-${var.deployment_target}-${var.owner}-${local.tstamp}"
  copy_tags_to_snapshot     = true
  delete_automated_backups  = false  # Do not delete backups on RDS instance termination
  apply_immediately         = true
}

output "rds_instance_address" {
  value = module.db.db_instance_address
}
