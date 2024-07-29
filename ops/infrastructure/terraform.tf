# ------------------------------------------------------------------------------
# DEPLOY A GIGADB APPLICATION IN AWS
# This terraform script sets up a complete GigaDB application in AWS. A VPC is 
# created in AWS cloud into which an EC2 instance hosting a Docker Host and a 
# RDS instance hosting the PostgreSQL database are launched into.
# ------------------------------------------------------------------------------


variable "aws_region" {
  type = string
  description = "AWS region where deployment occurs"
  default = "ap-east-1"
}

variable "deployment_target" {
  type = string
  description = "Environment to build"
  default = "staging"
}

variable "key_name" {
  type = string
  description = "Name of ssh key pair for EC2 access"
}

variable "gigadb_db_database" {
  type = string
  description = "Name of PostgreSQL database"
}

variable "gigadb_db_user" {
  type = string
  description = "Username for PostgreSQL database "
}

variable "gigadb_db_password" {
  type = string
  description = "Password for PostgreSQL database"
}

variable "snapshot_identifier" {
  type = string
  description = "Snapshot identifier for restoring RDS service"
  default = null
}

variable "restore_to_point_in_time" {
  type = map
  description = "A map to restoring RDS service from an automated backup"
  default = null
}

variable "source_dbi_resource_id" {
  type = string
  default = null
}

variable "use_latest_restorable_time" {
  type = bool
  default = null
}

variable "utc_restore_time" {
  type = string
  default = null
}

variable "web_ec2_type" {
  type = string
  description = "EC2 type for webapp server"
  default = null
}

variable "bastion_ec2_type" {
  type = string
  description = "EC2 type for bastion server"
  default = null
}

variable "rds_ec2_type" {
  type = string
  description = "EC2 type for RDS server"
  default = null
}



data "external" "callerUserName" {
  program = ["${path.module}/getIAMUserNameToJSON.sh"]
}

data "aws_availability_zones" "available" {
  state = "available"
}

provider "aws" {
  region     =  var.aws_region
  default_tags {  # These tags are copied into child modules and resources
    tags = {
      Environment = var.deployment_target,
      Owner = data.external.callerUserName.result.userName
    }
  }
}

terraform {
  backend "http" {
  }

  # required_providers {
  #   random = {
  #     source  = "hashicorp/random"
  #     version = "3.5.1"
  #   }

  #   external = {
  #     source  = "hashicorp/external"
  #     version = "2.3.1"
  #   }
    
  #   aws = {
  #     source  = "hashicorp/aws"
  #     version = "5.5.0"
  #   }
  # }

  # required_version = ">= 1.1"
}

# A custom virtual private cloud network for RDS and EC2 instances
module "vpc" {
  source  = "terraform-aws-modules/vpc/aws"
  version = "5.0.0"

  name = "vpc-${var.aws_region}-${var.deployment_target}-gigadb-${data.external.callerUserName.result.userName}"
  # CIDR block is a range of IPv4 addresses in the VPC. This cidr block below 
  # means that the main route table has the following routes: Destination = 
  # 10.99.0.0/18 , Target = local
  cidr = "10.99.0.0/18"

  # VPC spans all the availability zones in region
  azs = data.aws_availability_zones.available.names

  # We can add one or more subnets into each AZ. A subnet is required to launch
  # AWS resources into a VPC and is a range of IP addresses. Each subnet has a 
  # CIDR block which is a subset of the VPC CIDR block.

  # Public subnets will contain resources with public IP addresses and routes
  # A internet gateway is automatically created for these public subnets. An 
  # internet gateway exposes resources with public IPs to inbound traffic 
  # from the internet. All public subnets route to an Internet Gateway for 
  # non-local addresses which is what makes the subnet public.
  public_subnets   = ["10.99.0.0/24", "10.99.1.0/24", "10.99.2.0/24"]
  public_subnet_tags = {
    Name = "subnet-public"
  }

  # Private subnets contain resources that do not have public IPs. They have 
  # private IPs and can only interact with resources inside the same network
  # Resources in a private subnet needing internet access require a NAT device
  private_subnets  = ["10.99.3.0/24", "10.99.4.0/24", "10.99.5.0/24"]
  private_subnet_tags = {
     Name = "subnet-private"
  }

  database_subnets = ["10.99.6.0/24", "10.99.7.0/24", "10.99.8.0/24"]
  database_subnet_tags = {
    Name = "subnet-database"
  }

  # RDS instance will be launched into database subnet
  create_database_subnet_group = true
  # You can enable communication from internet to RDS via an internet gateway
  # to provide public access to RDS instance, but is not recommended for 
  # production! The parameters below are all false so no public access to RDS
  create_database_subnet_route_table = false
  create_database_internet_gateway_route = false

  # Required to access DNS server for installing postgresql package
  enable_dns_hostnames = true
  enable_dns_support = true

  # NAT gateways provide resources in private subnets that do not have
  # public IP address with outbound access to the public Internet or other AWS
  # resources. NAT gateways are placed in public subnet. Does RDS instance need 
  # a NAT as it will be placed in private subnet? Access to it will be via a 
  # bastion server.
  # enable_nat_gateway = false
  # single_nat_gateway = false
  # one_nat_gateway_per_az = false
}

output "vpc_id" {
  value = module.vpc.vpc_id
}

output "vpc_database_subnet_group" {
  value = module.vpc.database_subnet_group
}

# EC2 instance for hosting the web server 
module "ec2_dockerhost" {
  source = "../../modules/aws-instance"

  owner = data.external.callerUserName.result.userName
  deployment_target = var.deployment_target
  key_name = var.key_name
  eip_tag_name = "eip-gigadb-${var.deployment_target}-${data.external.callerUserName.result.userName}"
  vpc_id = module.vpc.vpc_id
  vpc_cidr_block = module.vpc.vpc_cidr_block
  # Locate Dockerhost EC2 instance in public subnet so users can access website
  # container app
  public_subnet_id = module.vpc.public_subnets[0]
  ec2_type = var.web_ec2_type
  ec2_usage = "webserver"
  app_port = 80
}

output "ec2_public_ip" {
  value = module.ec2_dockerhost.instance_public_ip_addr
}

output "ec2_private_ip" {
  value = module.ec2_dockerhost.instance_ip_addr
}

output "web_ec2_type" {
  value = module.ec2_dockerhost.instance_type
}

# EC2 instance for hosting the files server
module "files_host" {
  source = "../../modules/aws-instance"

  owner = data.external.callerUserName.result.userName
  deployment_target = var.deployment_target
  key_name = var.key_name
  eip_tag_name = "eip-gigadb-files-${var.deployment_target}-${data.external.callerUserName.result.userName}"
  vpc_id = module.vpc.vpc_id
  vpc_cidr_block = module.vpc.vpc_cidr_block
  public_subnet_id = module.vpc.public_subnets[0]
  ec2_type = var.web_ec2_type
  ec2_usage = "filesserver"
  app_port = 21
}

output "ec2_files_public_ip" {
  value = module.files_host.instance_public_ip_addr
}

output "ec2_files_private_ip" {
  value = module.files_host.instance_ip_addr
}

output "files_ec2_type" {
  value = module.files_host.instance_type
}

# EC2 instance for bastion server to access RDS for PostgreSQL admin
module "ec2_bastion" {
  source = "../../modules/bastion-aws-instance"

  owner = data.external.callerUserName.result.userName
  deployment_target = var.deployment_target
  key_name = var.key_name
  eip_tag_name = "eip-gigadb-bastion-${var.deployment_target}-${data.external.callerUserName.result.userName}"

  # Bastion instance goes into a public subnet for developer access
  vpc_id = module.vpc.vpc_id
  public_subnet_id = module.vpc.public_subnets[0]
  bastion_ec2_type = var.bastion_ec2_type
}

output "ec2_bastion_private_ip" {
  value = module.ec2_bastion.bastion_private_ip
}

output "ec2_bastion_public_ip" {
  description = "Public IP address of the EC2 bastion instance"
  value = module.ec2_bastion.bastion_public_ip
}

output "bastion_ec2_type" {
  value = module.ec2_bastion.instance_type
}

# RDS instance for hosting GigaDB's PostgreSQL database
module "rds" {
  source = "../../modules/rds-instance"

  owner = data.external.callerUserName.result.userName
  deployment_target = var.deployment_target

  # Needs to be overridden to restore an RDS snapshot
  snapshot_identifier = var.snapshot_identifier

  # Requires overrride.tf to restore RDS from an automated backup
  restore_to_point_in_time = var.restore_to_point_in_time

  vpc_id = module.vpc.vpc_id
  rds_subnet_ids = module.vpc.database_subnets
  vpc_database_subnet_group = module.vpc.database_subnet_group

  gigadb_db_database = var.gigadb_db_database
  gigadb_db_user = var.gigadb_db_user
  gigadb_db_password = var.gigadb_db_password

  # Security group rule required to allow port 5432 connections from private IP
  # of bastion server and ec2_dockerhost instance.

  rds_ec2_type = var.rds_ec2_type
}

output "rds_instance_address" {
  value = module.rds.rds_instance_address
}


################################################################################
# Provisioning of File System
################################################################################ 
module "gigadb_efs" {
  source = "../../modules/efs-filesystem"

  vpc = module.vpc
  deployment_target = var.deployment_target
  owner = data.external.callerUserName.result.userName
  
}

output "efs_filesystem_id" {
  value = module.gigadb_efs.id
}

output "efs_filesystem_arn" {
  value = module.gigadb_efs.arn
}

output "efs_filesystem_dns_name" {
  value = module.gigadb_efs.dns_name
}

output "efs_filesystem_size_in_bytes" {
  value = module.gigadb_efs.size_in_bytes
}

output "efs_filesystem_dropbox_area_id" {
  value = module.gigadb_efs.access_points["dropbox_area"].id
}

output "efs_filesystem_configuration_area_id" {
  value = module.gigadb_efs.access_points["configuration_area"].id
}

