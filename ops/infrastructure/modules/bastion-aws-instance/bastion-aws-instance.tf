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

  ingress {
    from_port   = 2376
    to_port     = 2376
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }
  
  ingress {
    from_port   = 9100
    to_port     = 9100
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

data "aws_ami" "centos" {
  most_recent = true

  filter {
    name   = "name"
    values = ["CentOS Stream 8 x86_64 *"]
  }

  filter {
    name   = "virtualization-type"
    values = ["hvm"]
  }

  owners = ["125523088429"]
}

resource "aws_instance" "bastion" {
  ami = data.aws_ami.centos.id
  associate_public_ip_address = true
  instance_type = "${var.bastion_ec2_type}"
  vpc_security_group_ids = [aws_security_group.bastion_sg.id]
  key_name = var.key_name
  subnet_id = var.public_subnet_id

  tags = {
    Name = "bastion_server_${var.deployment_target}_${var.owner}",
    System = "${var.bastion_ec2_type}_centos_stream8",
  }

  root_block_device {
    delete_on_termination = "true"
    volume_size = 30
  }

  volume_tags = {
    Owner = var.owner
    Environment = var.deployment_target
    Name = "bastion_server_volume_${var.deployment_target}"
  }
}

data "aws_eip" "bastion_host_eip" {
  filter {
    name   = "tag:Name"
    values = [var.eip_tag_name]
  }
}

resource "aws_eip_association" "docker_host_eip_assoc" {
  instance_id   = aws_instance.bastion.id
  allocation_id = data.aws_eip.bastion_host_eip.id
}

output "bastion_private_ip" {
  description = "EC2 bastion instance private IP address"
  value = aws_instance.bastion.private_ip
}

output "bastion_public_ip" {
  description = "EC2 bastion instance public IP address"
  value = aws_instance.bastion.public_ip
}

output "instance_type" {
  value = aws_instance.bastion.instance_type
}
