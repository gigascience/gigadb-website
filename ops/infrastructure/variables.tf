// AWS
variable "aws_access_key" {
  description = "AWS Access key"
}

variable "aws_secret_key" {
  description = "AWS secret key"
}

variable "aws_vpc_id" {
  description = "AWS VPC id"
}

variable "deployment_target" {
  description = "environment to build"
}

// Tencent Cloud
variable "tencent_secret_id" {
  description = "Tencent Cloud Secret ID"
}

variable "tencent_secret_key" {
  description = "Tencent Cloud secret key"
}

variable "tencent_appid" {
  description = "Tencent Cloud application ID"
}