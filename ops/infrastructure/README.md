# Infrastructure


Two types of files go here:


* **Terraform files**: to set up the low-level infrastructure for staging and production (and optionally development environment)

* **Ansible playbooks**: to provision the Docker infrastructure (for now, to keep simple, a Docker daemon but later it's gonna be containers orchestration)