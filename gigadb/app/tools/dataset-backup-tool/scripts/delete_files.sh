#!/usr/bin/env bash

set -e
set -u
source config/variables

MODE=${1:-"--dry-run"} # default to dry-run, pass --verbose to actually do the thing

echo "Enter the path to the file you want to delete (not including $BACKUP_REMOTE_ROOT):"
read
TARGET_FILE=$REPLY
read -p "Are you sure you want to delete $BACKUP_REMOTE_ROOT/$TARGET_FILE? (y/n) " -n 1 -r
if [[ $REPLY =~ ^[Yy]$ ]]
then
    echo "..."
    # do dangerous stuff
    rclone $MODE delete gigadb-backup:$BACKUP_BUCKET_FULLNAME$BACKUP_REMOTE_ROOT/$TARGET_FILE
fi


