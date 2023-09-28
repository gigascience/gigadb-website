#!/usr/bin/env bash

# bash shell wrapper for terraform-inventory.
# How to install the https://github.com/adammck/terraform-inventory command:
# brew install terraform-inventory
terraform-inventory $@ ./ | jq 'with_entries(select([.key] | inside(["system_t3_micro-centos8"])|not))'  
