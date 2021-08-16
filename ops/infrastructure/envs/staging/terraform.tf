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
  region     = "ap-east-1"
}


module "ec2" {
  source = "../../modules/aws-instance"

  deployment_target = var.deployment_target
  eip_tag_name = "eip-ape1-${var.deployment_target}-gigadb"
}