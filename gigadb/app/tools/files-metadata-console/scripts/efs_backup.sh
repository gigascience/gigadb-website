#!/usr/bin/env bash

# stop the script upon error
set -e

if [[ $(uname -n) =~ compute ]]; then
  source "./files-env"
else
  source "./.env"
  source "./.secrets"
fi

usage_message="Usage: $0 --doi <DOI> --sourcePath <Source Path>\n
Required:
--doi            Specify DOI to process
--sourcePath     Specify source path
--wasabi         Copy files to Wasabi bucket
--backup         Copy files to s3 bucket

Available Option:
--apply          Escape dry run mode

Example usages:
$0 --doi 100148 --sourcePath /share/dropbox/user101 --wasabi
$0 --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --apply
$0 --doi 100148 --sourcePath /share/dropbox/user101 --backup
$0 --doi 100148 --sourcePath /share/dropbox/user101 --backup --apply
$0 --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --backup
$0 --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --backup --apply"

# Check if DOI is provided
if [ $# -eq 0 ]; then
    echo -e "$usage_message"
    exit 1
fi

# Setup logging
function set_up_logging() {
  if [[ $(uname -n) =~ compute ]];then
    LOGDIR="/var/log/gigadb"
  else
    currentPath=$(pwd)
    LOGDIR="$currentPath/log"
  fi
  LOGFILE="$LOGDIR/backup_$(date +'%Y%m%d_%H%M%S').log"
  mkdir -p "${LOGDIR}"
  touch "${LOGFILE}"
}

#
# Rclone copy is executed in dry run mode as default. Use --apply flag to turn
# off dry run mode
dry_run=true

# By default, do not copy files to wasabi bucket
wasabi_upload=false

# By default, do not copy files to s3 bucket for backup
s3_upload=false

while [[ $# -gt 0 ]]; do
    case "$1" in
    --doi)
        doi=$2
        shift
        ;;
    --sourcePath)
        sourcePath=$2
        shift
        ;;
    --wasabi)
      wasabi_upload=true
        ;;
    --backup)
      s3_upload=true
        ;;
    --apply)
        dry_run=false
        ;;
    *)
        echo "Invalid option: $1"
        exit 1  ## Could be optional.
        ;;
    esac
    shift
done

# Check if neither --wasabi nor --backup is supplied
if [[ "${wasabi_upload}" == false && "${s3_upload}" == false ]]; then
    echo -e "Error: please specify --wasabi or --backup or both"
    echo -e "$usage_message"
    exit 1
fi

if [ "${doi}" -le 101000 ]; then
  dir_range="100001_101000"
elif [ "${doi}" -le 102000 ] && [ "${doi}" -ge 101001 ]; then
  dir_range="101001_102000"
elif [ "${doi}" -le 103000 ] && [ "${doi}" -ge 102001 ]; then
  dir_range="102001_103000"
else
  echo "DOI out of supported range"
  exit 1
fi

# Defaul rclone config
DEV_RCLONE_CONF_LOCATION='../wasabi-migration/config/rclone.conf'

# Construct the destination path
WASABI_DESTINATION_PATH="${WASABI_DATASETFILES_DIR}/${dir_range}/${doi}"
S3_DESTINATION_PATH="${S3_DATASETFILES_DIR}/${dir_range}/${doi}"

function copy_to_wasabi () {
  echo -e "$(date +'%Y/%m/%d %H:%M:%S') INFO  : Start copying files from $GIGADB_ENV to Wasabi" >> "${LOGFILE}"
  rclone_wasabi_cmd="rclone copy --s3-no-check-bucket ${sourcePath} ${WASABI_DESTINATION_PATH}"

  if [[ "${dry_run}" == true ]]; then
    rclone_wasabi_cmd+=" --dry-run"
  fi

  if [[ "${GIGADB_ENV}" == dev ]];then
    rclone_wasabi_cmd+=" --config ${DEV_RCLONE_CONF_LOCATION}"
  fi
  rclone_wasabi_cmd+=" --log-file ${LOGFILE}"
  rclone_wasabi_cmd+=" --log-level INFO"
  rclone_wasabi_cmd+=" --stats-log-level DEBUG"
  rclone_wasabi_cmd+=" >> ${LOGFILE}"
  # Execute command
  eval "${rclone_wasabi_cmd}"
  rclone_wasabi_exit_code=$?
  echo "$(date +'%Y/%m/%d %H:%M:%S') INFO  : Executed: ${rclone_wasabi_cmd}" >> "$LOGFILE"
  if [ ${rclone_wasabi_exit_code} -eq 0 ]; then
    echo -e "$(date +'%Y/%m/%d %H:%M:%S') INFO  : Successfully copied files to Wasabi bucket for DOI: $doi" >> "${LOGFILE}"
  else
    echo -e "$(date +'%Y/%m/%d %H:%M:%S') ERROR  : Problem with copying files to Wasabi bucket - rclone has exit code: ${rclone_wasabi_exit_code}" >> "${LOGFILE}"
  fi
}

function copy_to_s3 () {
    echo -e "$(date +'%Y/%m/%d %H:%M:%S') INFO  : Start copying files from $GIGADB_ENV to s3" >> "${LOGFILE}"
    rclone_s3_cmd="rclone copy --s3-no-check-bucket ${sourcePath} ${S3_DESTINATION_PATH}"

    if [ "${dry_run}" == true ]; then
      rclone_s3_cmd+=" --dry-run"
    fi

    if [[ "${GIGADB_ENV}" == dev ]];then
        rclone_s3_cmd+=" --config ${DEV_RCLONE_CONF_LOCATION}"
    fi

    rclone_s3_cmd+=" --log-file ${LOGFILE}"
    rclone_s3_cmd+=" --log-level INFO"
    rclone_s3_cmd+=" --stats-log-level DEBUG"
    rclone_s3_cmd+=" >> ${LOGFILE}"

    eval "${rclone_s3_cmd}"
    rclone_s3_exit_code=$?
    echo "$(date +'%Y/%m/%d %H:%M:%S') INFO  : Executed: ${rclone_s3_cmd}" >> "$LOGFILE"
    if [ ${rclone_s3_exit_code} -eq 0 ]; then
      echo -e "$(date +'%Y/%m/%d %H:%M:%S') INFO  : Successfully copied files to s3 bucket for DOI: $doi" >> "${LOGFILE}"
    else
      echo -e "$(date +'%Y/%m/%d %H:%M:%S') ERROR  : Problem with copying files to s3 bucket - rclone has exit code: ${rclone_s3_exit_code}" >> "${LOGFILE}"
    fi
}

set_up_logging

if [[ "${wasabi_upload}" == true ]];then
  copy_to_wasabi
fi

if [[ "${s3_upload}" == true ]];then
  copy_to_s3
fi
