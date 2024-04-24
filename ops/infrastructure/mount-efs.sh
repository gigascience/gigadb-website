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

# TODO: install aws efs utils
# by following this doc: https://github.com/aws/efs-utils?tab=readme-ov-file#on-other-linux-distributions
# Wait for cloud-init to complete
#while [ ! -f /var/lib/cloud/instance/boot-finished ]; do
#    echo "Waiting for cloud-init to complete..."
#    sleep 5
#done
# by following https://github.com/aws/efs-utils?tab=readme-ov-file#on-other-linux-distributions
#sudo yum update -y
#sudo yum -y install git rpm-build make rust cargo openssl-devel
#git clone https://github.com/aws/efs-utils
#cd efs-utils
#sudo make rpm
#sudo yum -y install build/amazon-efs-utils*rpm

# TODO: mount accesspoint
#sudo mount -t efs -o tls,accesspoint=$fasp_dropbox $fs_id efs-mountpoint/dropbox
#sudo mount -t efs -o tls,accesspoint=$fasp_config $fs_id efs-mountpoint/config

# TODO: make a permanent mount
#echo "$fs_id /efs-mountpoint/dropbox _netdev,tls,accesspoint=$fasp_dropbox 0 0" >> /etc/fstab
#echo "$fs_id /efs-mountpoint/config _netdev,tls,accesspoint=$fasp_config 0 0" >> /etc/fstab


