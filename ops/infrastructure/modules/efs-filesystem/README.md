# efs-filesystem

```
data "aws_availability_zones" "available" {
  state = "available"
}

resource "aws_efs_file_system" "dropbox_filesystem" {

  creation_token = "gigadb-dropbox-efs-${var.owner}-${var.deployment_target}"
  lifecycle_policy {
    transition_to_ia                    = "AFTER_30_DAYS"
    transition_to_primary_storage_class = "AFTER_1_ACCESS"
  }

}

resource "aws_security_group" "dropbox_sg" {
  name        = "dropbox-sg_${var.deployment_target}_${var.owner}"
  description = "Security group for dropbox ${var.owner} on ${var.deployment_target}"
  vpc_id      = var.vpc_id

  ingress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["10.99.0.0/18"]
  }
}

resource "aws_efs_mount_target" "dropbox_mount_target" {
  file_system_id  = aws_efs_file_system.dropbox_filesystem.id
  security_groups = [aws_security_group.dropbox_sg.name]
  subnet_id       = var.public_subnet_id
}

resource "aws_efs_access_point" "dropbox_accesspoint" {
  file_system_id  = aws_efs_file_system.dropbox_filesystem.id
  posix_user {
    uid = 1000
    gid = 1000
  }

  root_directory {
    path = "/gigadb-dropbox"
  }
  
}

```