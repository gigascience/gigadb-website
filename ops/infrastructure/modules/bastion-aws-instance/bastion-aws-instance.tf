resource "aws_security_group" "bastion_sg" {
  name        = "bastion_sg_${var.deployment_target}_${var.owner}"
  description = "Allow connection to bastion server for ${var.deployment_target}"
  vpc_id      = var.vpc_id

  # Allowing access from public internet - it is expected that developers will
  # terraform destroy the bastion instance once finished performing PostgreSQL
  # admin tasks
  ingress {
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }
  
   tags = {
     Name = "bastion_sg_${var.deployment_target}_${var.owner}"
   }
}

resource "aws_instance" "bastion" {
  ami = "ami-0b197b1f02309cb3c"  # Centos 8
  associate_public_ip_address = true
  instance_type = "t3.micro"
  vpc_security_group_ids = [aws_security_group.bastion_sg.id]
  key_name = var.key_name
  subnet_id = var.public_subnet_id

  tags = {
    Name = "bastion_server_${var.deployment_target}_${var.owner}",
    System = "t3_micro-centos8",
  }

  root_block_device {
    delete_on_termination = "true"
  }

  volume_tags = {
    Owner = var.owner
    Environment = var.deployment_target
    Name = "bastion_server_volume_${var.deployment_target}"
  }
}

output "bastion_private_ip" {
  description = "EC2 bastion instance private IP address"
  value = aws_instance.bastion.private_ip
}

output "bastion_public_ip" {
  description = "EC2 bastion instance public IP address"
  value = aws_instance.bastion.public_ip
}
