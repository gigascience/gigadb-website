#!/usr/bin/env bash

# set up the appropriate network
http_proxy=http://proxy.sz.cngb.org:3128
https_proxy=http://proxy.sz.cngb.org:3128
ftp_proxy=http://proxy.sz.cngb.org:3128
no_proxy="127.0.0.1,localhost,*.cloud.am55.*,*.sz.cngb.org,*.genomics.cn"
export http_proxy
export https_proxy
export ftp_proxy
export no_proxy


date=$(date +"%Y%m%d%H%M")

rclone --config=/home/gigadb/ken/rclone.conf copy --checksum --log-level DEBUG --log-file=/home/gigadb/ken/logs/upload_to_us1_$date.log /home/gigadb/ken/test-download-from-tyo/NA12878 wasabiKenUs:test-us-east1-bucket/test-upload/NA12878
rclone --config=/home/gigadb/ken/rclone.conf purge --checksum --log-level DEBUG --log-file=/home/gigadb/ken/logs/upload_to_us1_$date.log  wasabiKenUs:test-us-east1-bucket/test-upload/NA12878
