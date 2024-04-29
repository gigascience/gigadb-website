#!/bin/bash
set -ex

# Define the base mount path
baseMountPath="/share"

# Create the mount directory if it doesn't exist
if [ ! -d "$baseMountPath" ]; then
  sudo mkdir -p "$baseMountPath" "$baseMountPath/dropbox" "$baseMountPath/config"
  sudo chown -R  centos:centos "$baseMountPath"
fi

# install aws efs utils
curl https://s3.ap-northeast-1.wasabisys.com/infra-resources/amazon-efs-utils-2.0.1-1.el8.x86_64.rpm -o aws-efs-utils.rpm
sudo yum install -y aws-efs-utils.rpm

# mount accesspoint
if [[ -n "${fsap_dropbox_id}" ]];then
  sudo mount -t efs -o tls,accesspoint="${fsap_dropbox_id}" "${fs_id}" "$baseMountPath/dropbox"
fi

if [[ -n "${fsap_config_id}" ]];then
  sudo mount -t efs -o tls,accesspoint="${fsap_config_id}" "${fs_id}" "$baseMountPath/config"
fi

