locals {
  tstamp = formatdate("YYYYMMDDhhmmss", timestamp())
}

module "security_group" {
  source  = "terraform-aws-modules/security-group/aws"
  version = "5.1.0"

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
  version = "6.0.0"
  identifier = "rds-server-${var.deployment_target}-${var.owner}"

  snapshot_identifier = var.snapshot_identifier
  restore_to_point_in_time = var.restore_to_point_in_time

  db_name                     = var.gigadb_db_database
  username                    = var.gigadb_db_user
  manage_master_user_password = false
  password                    = var.gigadb_db_password
  port                        = 5432

  # Create this RDS instance in database subnet group in VPC
  db_subnet_group_name   = var.vpc_database_subnet_group
  vpc_security_group_ids = [module.security_group.security_group_id]

  create_db_option_group    = false
  create_db_parameter_group = false

  parameter_group_name      = (var.deployment_target == "staging" ? aws_db_parameter_group.gigadb-db-param-group[0].name : null)
  engine                    = "postgres"
  engine_version            = "14.8"
  family                    = "postgres14"  # DB parameter group
  major_engine_version      = "14"          # DB option group
  instance_class            = "db.${var.rds_ec2_type}"
  allocated_storage         = 16
  deletion_protection       = false
  maintenance_window        = "Mon:00:00-Mon:03:00"
  backup_window             = "03:00-06:00"  # UTC time
  backup_retention_period   = 5  # days
  skip_final_snapshot       = false  # Create final snapshot
  final_snapshot_identifier_prefix = "snapshot-final-${var.deployment_target}-${var.owner}-${local.tstamp}"
  copy_tags_to_snapshot     = true
  delete_automated_backups  = false  # Do not delete backups on RDS instance termination
  apply_immediately         = true
}

resource "aws_db_parameter_group" "gigadb-db-param-group" {
  count = var.deployment_target == "staging" ? 1 : 0
  name = "gigadb-db-param-group-${var.owner}"
  description = "DB parameter group for staging server"
  family = "postgres14"

  parameter {
    apply_method = "immediate"
    name = "log_statement"
    value = "all"
  }

  parameter {
    apply_method = "immediate"
    name = "log_min_duration_statement"
    value = "0"  # Log all SQL statements
  }
}

output "rds_instance_address" {
  value = module.db.db_instance_address
}
