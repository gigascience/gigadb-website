#!/usr/bin/env bash

set -e
set -u
source config/variables

MODE=${1:-"--dry-run"} # default to dry-run, pass --verbose to actually do the thing

rclone $MODE --checksum sync $BACKUP_LOCAL_ROOT gigadb-backup:$BACKUP_BUCKET_FULLNAME$BACKUP_REMOTE_ROOT