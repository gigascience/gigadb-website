#!/usr/bin/env bash

set -e

echo "Starting processing FTP upload..."
cd /app && find /var/inbox -mindepth 1 -not -empty -type d  -exec bash -c './yii ftp/process-upload --dataset_dir "$0"'  {} \;
