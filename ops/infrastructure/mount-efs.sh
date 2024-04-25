#!/bin/bash
set -ex

# Define the base mount path
baseMountPath="/share"

# Define efs variables
fs_id="${fs_id}"
fasp_dropbox="${fsap_dropbox}"
fasp_config="${fsap_config}"

# Create the mount directory if it doesn't exist
if [ ! -d "$baseMountPath" ]; then
  sudo mkdir -p "$baseMountPath" "$baseMountPath/dropbox" "$baseMountPath/config"
  sudo chown -R  centos:centos "$baseMountPath"
fi

# install aws efs utils
curl https://s3.ap-northeast-1.wasabisys.com/infra-resources/amazon-efs-utils-2.0.1-1.el8.x86_64.rpm -o aws-efs-utils.rpm
sudo yum install -y aws-efs-utils.rpm

# TODO: mount accesspoint
#sudo mount -t efs -o tls,accesspoint=$fasp_dropbox $fs_id efs-mountpoint/dropbox
#sudo mount -t efs -o tls,accesspoint=$fasp_config $fs_id efs-mountpoint/config

# TODO: make a permanent mount
#echo "$fs_id /efs-mountpoint/dropbox _netdev,tls,accesspoint=$fasp_dropbox 0 0" >> /etc/fstab
#echo "$fs_id /efs-mountpoint/config _netdev,tls,accesspoint=$fasp_config 0 0" >> /etc/fstab


