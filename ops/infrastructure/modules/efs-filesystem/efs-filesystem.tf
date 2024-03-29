locals {

  azs = slice(data.aws_availability_zones.available.names, 0, 3)

}

data "aws_availability_zones" "available" {
  state = "available"
}

data "aws_caller_identity" "current" {}

data "aws_region" "current" {}

module "efs" {
  source = "terraform-aws-modules/efs/aws"

  # File system
  name           = "gigadb-efs ${var.owner} ${var.deployment_target}"
  creation_token = "gigadb-efs-${var.owner}-${var.deployment_target}"
  encrypted      = false


  lifecycle_policy = {
    transition_to_ia                    = "AFTER_30_DAYS"
    transition_to_primary_storage_class = "AFTER_1_ACCESS"
  }

  # File system policy
  attach_policy                      = false
  bypass_policy_lockout_safety_check = false
  

  # Performance profile
  performance_mode                = "generalPurpose"
  throughput_mode                 = "elastic"
  
  # Mount targets / security group
  mount_targets              = { for k, v in zipmap(local.azs, var.vpc.private_subnets) : k => { subnet_id = v } }

  security_group_description = "gigadb-efs EFS SG for ${data.aws_caller_identity.current.arn} on ${var.deployment_target}"
  security_group_vpc_id      = var.vpc.vpc_id
  security_group_rules = {
    vpc = {
      # relying on the defaults provided for EFS/NFS (2049/TCP + ingress)
      description = "NFS ingress from VPC private subnets"
      cidr_blocks = var.vpc.private_subnets_cidr_blocks
    }
  }

  # Access point(s)
  access_points = {
    dropbox_area = {

      name = "dropbox-area-${data.aws_caller_identity.current.arn}-${var.deployment_target}"

      posix_user = {
        gid            = 1000
        uid            = 1000
      }

      root_directory = {
        path = "/share/dropbox"
        creation_info = {
          owner_gid   = 1000
          owner_uid   = 1000
          permissions = "755"
        }
      }

    }

    configuration_area = {

      name = "config-area-${data.aws_caller_identity.current.arn}-${var.deployment_target}"

      posix_user = {
        gid            = 1000
        uid            = 1000
      }

      root_directory = {
        path = "/share/config"
        creation_info = {
          owner_gid   = 1000
          owner_uid   = 1000
          permissions = "700"
        }
      }

    }

  }

  # Backup policy
  enable_backup_policy = false

  # Replication (to another region) configuration (see https://docs.aws.amazon.com/efs/latest/ug/efs-replication.html)
  create_replication_configuration = false
  # replication_configuration_destination = {
  #   region = data.aws_region.current.name
  # }

  tags = {
    Owner   = var.owner
    Environment = var.deployment_target
  }
}