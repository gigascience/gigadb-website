variable "aws_access_key" {
  type = string
  description = "AWS Access key"
  sensitive = true
}

variable "aws_secret_key" {
  type = string
  description = "AWS secret key"
  sensitive = true
}

variable "aws_vpc_id" {
  type = string
  description = "AWS VPC id"
}

variable "deployment_target" {
  type = string
  description = "environment to build"
  default = "staging"
}


terraform {
    backend "http" {
    }
}

provider "aws" {
  access_key = var.aws_access_key
  secret_key = var.aws_secret_key
  region     = "ap-southeast-1"
}


module "ec2" {
  source = "../../modules/aws-instance"

  vpc_id = var.aws_vpc_id
  deployment_target = var.deployment_target
  eip_tag_name = "eip-${var.deployment_target}"
}