resource "aws_security_group" "docker_host_sg" {
  name        = "docker_host_sg_${var.deployment_target}_${var.owner}"
  description = "Allow connection to docker host for ${var.deployment_target}"
  vpc_id      = var.vpc_id

  ingress {
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    from_port   = 9100
    to_port     = 9100
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["10.99.0.0/18"]
  }

  ingress {
    from_port   = 2376
    to_port     = 2376
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    from_port   = 30000
    to_port     = 30009
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    from_port   = 9021
    to_port     = 9021
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  egress {
    from_port   = 9135
    to_port     = 9135
    protocol    = "tcp"
    cidr_blocks = [var.vpc_cidr_block]
  }

   tags = {
     Name = "docker_host_sg_${var.deployment_target}_${var.owner}"
   }
}

data "aws_ami" "centos" {
  most_recent = true

  filter {
    name   = "name"
    values = ["CentOS Stream 8 x86_64 20231127"]
  }

  filter {
    name   = "virtualization-type"
    values = ["hvm"]
  }

  owners = ["125523088429"]
}

resource "aws_instance" "docker_host" {
  ami = data.aws_ami.centos.id
  instance_type = "${var.web_ec2_type}"
  vpc_security_group_ids = [aws_security_group.docker_host_sg.id]
  key_name = var.key_name
  subnet_id = var.public_subnet_id

  tags = {
    Name = "gigadb_server_${var.deployment_target}_${var.owner}",
    System = "${var.web_ec2_type}_centos_stream8",
  }

  root_block_device {
    delete_on_termination = "true"
    volume_size = 30
  }

  volume_tags = {
    Owner = var.owner
    Name = "gigadb_server_volume_${var.deployment_target}"
    Environment = var.deployment_target
  }
}

data "aws_eip" "docker_host_eip" {
  filter {
    name   = "tag:Name"
    values = [var.eip_tag_name]
  }
}

resource "aws_eip_association" "docker_host_eip_assoc" {
  instance_id   = aws_instance.docker_host.id
  allocation_id = data.aws_eip.docker_host_eip.id
}

output "instance_ip_addr" {
  value = aws_instance.docker_host.private_ip
}

output "instance_public_ip_addr" {
  value = aws_instance.docker_host.public_ip
}

output "instance_type" {
  value = aws_instance.docker_host.instance_type
}
