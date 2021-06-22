# Declare TencentCloud provider
terraform {
  required_providers {
    tencentcloud = {
      source = "tencentcloudstack/tencentcloud"
      version = "1.56.8"
    }
  }
}

# Configure TencentCloud provider
provider "tencentcloud" {
  secret_id  = var.tencent_secret_id
  secret_key = var.tencent_secret_key
  region     = "ap-guangzhou"
}

# Create private bucket
resource "tencentcloud_cos_bucket" "bucket1" {
  # Bucket name consists of [custom name]-[Account AppId]
  bucket            = "bucket1-${var.tencent_appid}"
  acl               = "private"
//  log_enable        = true
//  log_target_bucket = "mylog-${var.tencent_appid}"
//  log_prefix        = "bucket1"
}
