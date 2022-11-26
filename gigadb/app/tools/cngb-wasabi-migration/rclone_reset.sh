#!/usr/bin/env bash

# Bail out upon error
set -e

# Allow all scripts to base include, log, etc. paths off the
# directory where backup script is located
APP_HOME=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )

# Setup logging
LOGDIR="$APP_HOME/logs"
LOGFILE="$LOGDIR/reset_$(date +'%Y%m%d_%H%M%S').log"
mkdir -p $LOGDIR
touch "$LOGFILE"

# Delete datasets
doi_dir_path1="wasabi:gigadb-datasets/dev/pub/10.5524/100001_101000/100002"
doi_dir_path2="wasabi:gigadb-datasets/dev/pub/10.5524/100001_101000/100012"

rclone delete "$doi_dir_path1" \
    --log-file="$LOGFILE" \
    --log-level INFO \
    --stats-log-level DEBUG >> "$LOGFILE"
    
rclone delete "$doi_dir_path2" \
    --log-file="$LOGFILE" \
    --log-level INFO \
    --stats-log-level DEBUG >> "$LOGFILE"