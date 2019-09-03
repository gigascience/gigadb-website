resource "aws_security_group" "docker_host_sg" {
  name        = "docker_host_sg"
  description = "Allow connection to docker host for ${var.deployment_target}"
  vpc_id      = "${var.vpc_id}"

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

  egress {
  from_port   = 0
  to_port     = 0
  protocol    = "-1"
  cidr_blocks = ["0.0.0.0/0"]
  }
}


resource "aws_instance" "docker_host" {
  ami = "ami-8e0205f2"
  instance_type = "t2.micro"
  vpc_security_group_ids = ["${aws_security_group.docker_host_sg.id}"]
  key_name = "aws-centos7-keys"

  tags = {
    Name = "ec2-as1-${var.deployment_target}"
  }

  root_block_device = {
    delete_on_termination = "true"
  }
}

data "aws_eip" "docker_host_eip" {
  filter {
    name   = "tag:Name"
    values = ["${var.eip_tag_name}"]
  }
}

resource "aws_eip_association" "docker_host_eip_assoc" {
  instance_id   = "${aws_instance.docker_host.id}"
  allocation_id = "${data.aws_eip.docker_host_eip.id}"
}
