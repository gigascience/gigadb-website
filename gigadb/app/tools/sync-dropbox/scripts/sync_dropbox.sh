#!/usr/bin/env bash

# stop the script upon error
set -e

if [[ $(uname -n) =~ compute ]]; then
  source "./files-env"
else
  source "./.env"
fi

usage_message="Usage: $0 <option> \n

Available Option:
--dry-run        Do a trial run
--apply          Escape dry run mode

Example usages:
$0 --dry-run
$0 --apply"

if [ $# -eq 0 ]; then
    echo -e "$usage_message"
    exit 1
fi

# Setup logging
function set_up_logging() {
  if [[ $(uname -n) =~ compute ]];then
    LOGDIR="/var/logs/sync_dropbox/"
  else
    currentPath=$(pwd)
    LOGDIR="$currentPath/logs"
  fi
  LOGFILE="$LOGDIR/sync_dropbox_$(date +'%Y%m%d_%H%M%S').log"
  mkdir -p "${LOGDIR}"
  touch "${LOGFILE}"
}


# rclone sync is executed in dry run mode as default
dry_run=true

while [[ $# -gt 0 ]]; do
    case "$1" in
    --dry-run)
        dry_run=true
        ;;
    --apply)
        dry_run=false
        ;;
    *)
        echo "Invalid option: $1"
        exit 1
        ;;
    esac
    shift
done

# Dev rclone config
DEV_RCLONE_CONF_LOCATION='config/rclone.conf'

function start_sync () {
  echo -e "$(date +'%Y/%m/%d %H:%M:%S') INFO : Start sync dropbox from production to alt-production" >> "${LOGFILE}"
  rclone_sync_cmd="rclone sync production-staging:/share/dropbox/ /share/dropbox"
  
  if [[ "${dry_run}" == true ]]; then
      rclone_sync_cmd+=" --dry-run"
  fi

  if [[ "${GIGADB_ENV}" == dev ]];then
    rclone_sync_cmd+=" --config ${DEV_RCLONE_CONF_LOCATION}"
  fi

  rclone_sync_cmd+=" --log-file ${LOGFILE}"
  rclone_sync_cmd+=" --log-level INFO"
  rclone_sync_cmd+=" --stats-log-level DEBUG"
  rclone_sync_cmd+=" >> ${LOGFILE}"
  # Execute command
  eval "${rclone_sync_cmd}"
  rclone_sync_exit_code=$?
  
  echo "$(date +'%Y/%m/%d %H:%M:%S') INFO  : Executed: ${rclone_sync_cmd}" >> "$LOGFILE"
  if [ ${rclone_sync_exit_code} -eq 0 ]; then
    echo -e "$(date +'%Y/%m/%d %H:%M:%S') INFO  : Successfully sync dropbox from production to alt-production" >> "${LOGFILE}"
  else
    echo -e "$(date +'%Y/%m/%d %H:%M:%S') ERROR  : Problem with the sync - rclone has exit code: ${rclone_sync_exit_code}" >> "${LOGFILE}"
  fi
}

set_up_logging
start_sync