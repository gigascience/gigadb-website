#!/usr/bin/env bash

# bails on error
set -e

# print command being run
set -x

if ! [ -f  ./.rclone.conf ];then
  read -p "To create rclone config, enter your wasabi access_key_id: " key
  read -p "To create rclone config, enter your wasabi secret_access_key: " secret
  read -p "To create rclone config, enter your wasabi region: " region
  cp config-source/rclone.config.template .rclone.conf
  sed -i'.bak' "s/access_key_id =/access_key_id = $key/" .rclone.conf
  sed -i'.bak' "s/secret_access_key =/secret_access_key = $secret/" .rclone.conf
  sed -i'.bak' "s/region =/region = $region/" .rclone.conf
  rm .rclone.conf.bak
fi