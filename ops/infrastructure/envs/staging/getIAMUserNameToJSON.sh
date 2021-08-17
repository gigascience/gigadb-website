#!/bin/bash

# this script is to be called in terraform external resource data block in terraform.tf
# Its purpose is to extract the user name of the IAM user who is calling terraform
# So that it can be used to set a default tag Owner with that user name as value
# the output has to be valid JSON

set -e
userName=$(aws sts get-caller-identity --output text --query Arn | cut -d"/" -f2)
jq -n --arg userName "$userName" '{"userName":$userName}'