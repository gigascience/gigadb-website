#!/usr/bin/env bash

# stop the script upon error
set -e

if [[ $(uname -n) =~ compute ]]; then
  source "./.bash_profile"
  RCLONE_CONF='/etc/sync_dropbox/rclone.conf'
else
  source "./.env"
  RCLONE_CONF='config/rclone.conf'
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
    LOGDIR="/var/log/gigadb/sync_dropbox"
  else
    currentPath=$(pwd)
    LOGDIR="$currentPath/logs"
    SHAREDATADIR="data/share/dropbox"
  fi
  LOGFILE="$LOGDIR/sync_dropbox_$(date +'%Y%m%d_%H%M%S').log"
  mkdir -p "${LOGDIR}" "${SHAREDATADIR}"
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

function start_sync () {
 # Determine the rclone sync command based on the environment
  case "${GIGADB_ENV}" in
    dev)
      ENV=$(awk 'NR==1 {print $1}' ${RCLONE_CONF} | tr -d '[]')
      echo -e "$(date +'%Y/%m/%d %H:%M:%S') INFO : Start sync dropbox from ${ENV} to alt ${GIGADB_ENV}" | tee -a "${LOGFILE}"
      rclone_sync_cmd="rclone sync production-staging:/share/dropbox/ ${SHAREDATADIR}"
      ;;
    staging)
      ENV=$(awk 'NR==1 {print $1}' ${RCLONE_CONF} | tr -d '[]')
      echo -e "$(date +'%Y/%m/%d %H:%M:%S') INFO : Start sync dropbox from ${ENV} to alt ${GIGADB_ENV}" | tee -a "${LOGFILE}"
      rclone_sync_cmd="rclone sync production-staging:/share/dropbox/ /share/dropbox"
      ;;
    live)
      ENV=$(awk 'NR==10 {print $10}' ${RCLONE_CONF} | tr -d '[]')
      echo -e "$(date +'%Y/%m/%d %H:%M:%S') INFO : Start sync dropbox from ${ENV} to alt ${GIGADB_ENV}" | tee -a "${LOGFILE}"
      rclone_sync_cmd="rclone sync production-live:/share/dropbox/ /share/dropbox"
      ;;
  esac

  # Append options
  [[ "${dry_run}" == true ]] && rclone_sync_cmd+=" --dry-run"
  rclone_sync_cmd+=" --config ${RCLONE_CONF}"
  rclone_sync_cmd+=" --log-file ${LOGFILE} --log-level INFO --stats-log-level DEBUG"

  # Execute command
  eval "${rclone_sync_cmd}"
  rclone_sync_exit_code=$?
  
  echo "$(date +'%Y/%m/%d %H:%M:%S') INFO  : Executed: ${rclone_sync_cmd}" | tee -a "$LOGFILE"
  if [ ${rclone_sync_exit_code} -eq 0 ]; then
    echo -e "$(date +'%Y/%m/%d %H:%M:%S') INFO  : Successfully sync dropbox from ${ENV} to alt ${GIGADB_ENV}" | tee -a "${LOGFILE}"
  else
    echo -e "$(date +'%Y/%m/%d %H:%M:%S') ERROR  : Problem with the sync - rclone has exit code: ${rclone_sync_exit_code}" | tee -a "${LOGFILE}"
  fi
}

set_up_logging
start_sync