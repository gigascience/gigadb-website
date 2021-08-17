data "aws_vpc" "default" {
  default = true
}

resource "aws_security_group" "docker_host_sg" {
  name        = "docker_host_sg"
  description = "Allow connection to docker host for ${var.deployment_target}"
  vpc_id      = data.aws_vpc.default.id

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
   tags = {
     Name = var.deployment_target
   }
}


resource "aws_instance" "docker_host" {
  ami = "ami-68e59c19"
  instance_type = "t3.micro"
  vpc_security_group_ids = [aws_security_group.docker_host_sg.id]
  key_name = "aws-hk-centos7-keys"

  tags = {
    Name = "gigadb_server_${var.deployment_target}",
    System = "t3_micro-centos7",
  }

  root_block_device {
    delete_on_termination = "true"
  }

  volume_tags = {
    Owner = var.owner
    Name = "gigadb_server_root_device"
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
