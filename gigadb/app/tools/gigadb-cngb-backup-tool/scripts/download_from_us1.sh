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

rclone --config=/home/gigadb/ken/rclone.conf copy --checksum --log-level DEBUG --log-file=/home/gigadb/ken/logs/download_from_us1_$date.log wasabiKenUs:test-us-east1-bucket/test-download/NA12878 /home/gigadb/ken/test-download-from-us1/NA12878