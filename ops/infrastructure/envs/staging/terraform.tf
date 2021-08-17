variable "deployment_target" {
  type = string
  description = "environment to build"
  default = "staging"
}



terraform {
    backend "http" {
    }
}



data "external" "callerUserName" {
  program = ["${path.module}/getIAMUserNameToJSON.sh"]
}

provider "aws" {
  region     = "ap-east-1"
  default_tags {
      tags = {
        Environment = var.deployment_target,
        Owner = data.external.callerUserName.result.userName
      }
    }


}


module "ec2" {
  source = "../../modules/aws-instance"

  owner = data.external.callerUserName.result.userName
  deployment_target = var.deployment_target
  eip_tag_name = "eip-ape1-${var.deployment_target}-gigadb"
}