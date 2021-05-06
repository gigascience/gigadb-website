provider "aws" {
  access_key = var.aws_access_key
  secret_key = var.aws_secret_key
  region     = "ap-southeast-1"
}


module "ec2" {
  source = "../../modules/aws-instance"

  vpc_id = var.aws_vpc_id
  deployment_target = "${var.deployment_target}"
  eip_tag_name = "eip-${var.deployment_target}"
}