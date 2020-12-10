# Infrastructure


Two types of files go here:


* **Terraform files**: to set up the low-level infrastructure for staging and production

* **Ansible playbooks**: to provision the Docker platform.


## Terraform (for managing the infrastructure)

To set Terraform variables, either pass the -var option to ``terraform`` command or set environment variables following the pattern **TF_VAR_variable_name_here**

You need to export variables for:
* aws\_access\_key
* aws\_secret\_key
* aws\_vpc\_id
* deployment_target

Make sure the ``.terraform`` directory and the ``*.tfstate`` and ``*.tfstate.backup`` files are NOT version controlled.

If more than one person need to manage the infrastructure, make sure everyone has share an up-to-date copy of the ``*.tfstate`` file.

### Usage

```
$ cd ops/infrastructure
$ export TF_STATE=.
$ export TF_VAR_deployment_target=staging
$ export TF_VAR_aws_vpc_id=<AWS VPC id>
$ export TF_VAR_aws_access_key=<AWS Access key>
$ export TF_VAR_aws_secret_key=<AWS Secret key>

$ terraform plan
$ terraform apply
$ terraform refresh
```

## Ansible (for managing the platform)

Use ``ops/infrastructure/staging-playbook.yml`` for configuring the staging server with docker daemon and postgresql.
For production, copy the staging playbook into ``ops/infrastructure/production-playbook.yml`` and adjust the **"hosts:"** yaml dictionary key with value: **"production_dockerhost"**.


make sure the password to the vault, which will be communicated out-of-band alongside the vault file, is stored in ``~/.vault_pass.txt``.
The vault file should be placed at ``ops/infrastructure/group_vars/all/vault``. Make sure the vault file is NOT version controlled.

The Ansible playbook needs to talk to Gitlab.com API in order to store variables for TLS certificates and server IP addresses (later used by Docker compose and Gitlab CI), so your private Gitlab token need to be written to a file (by default $HOME/.gitlab\_private\_token).

**Important:** You need to configure details for the staging and production servers in the ``ops/infrastructure/inventories/hosts`` file under the [\*:vars] sections.


Use ``ansible-vault edit <path to vault file>`` to edit the content of the vault in order to adjust the database credentials.

### Usage:

```
$ cd ops/infrastructure
$ ansible-playbook -i inventories staging-playbook.yml --vault-password-file ~/.vault_pass.txt
```

Increate verbosity for debug by adding `` -vvv``  to the ``ansible-playbook`` command.
