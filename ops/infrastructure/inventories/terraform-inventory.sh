#!/usr/bin/env bash

# bash shell wrapper for terraform-inventory.
# How to install the https://github.com/adammck/terraform-inventory command:
# brew install terraform-inventory
# the right side of the pipe is to prevent system tags created by AWS to be passed on to Ansible
# which can trigger warnings when excecuting playbooks as those tags can contains illegal hyphen caracters
terraform-inventory $@ ./ | jq 'with_entries(select([.key] | contains(["system_"])|not))'  
