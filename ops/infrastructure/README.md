# Infrastructure


Two types of files go here:


* **Terraform files**: to set up the low-level infrastructure for staging and production (and optionally development environment)

* **Ansible playbooks**: to provision the Docker infrastructure (for now, to keep simple, a Docker daemon but later it's gonna be containers orchestration)


## Terraform

To set Terraform variables, either pass the -var option to ``terraform`` command or set environment variables following the pattern **TF_VAR_variable_name_here**

Make sure the ``.terraform`` directory and the ``*.tfstate`` and ``*.tfstate.backup`` files are NOT version controlled.