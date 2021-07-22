#!/usr/bin/env bash

set -e

find /data/gigadb/pub/10.5524/ ! -perm -g+r,u+r,o+r -exec chmod a+r {} \; >> /tmp/permission_cron 2>&1