#!/usr/bin/env bash

# stop the script upon error
set -e

# Check if DOI is provided
if [ $# -eq 0 ]; then
    echo "Error: DOI is required!"
    echo "Usage: $0 <DOI> <Source Path>"
    echo "uploads dataset files to the aws s3 bucket - gigadb-datasets-metadata and the wasabi bucket - gigadb-datasets"
    echo "Use $0 <DOI> <Source Path> --apply to escape dry run mode"
    echo "Use $0 <DOI> <Source Path> --use_live_data to upload to live buckets"
    exit 1
fi

# Setup logging
currentPath=$(pwd)
LOGDIR="$currentPath/uploadDir"
LOGFILE="$LOGDIR/backup_$(date +'%Y%m%d_%H%M%S').log"
mkdir -p "${LOGDIR}"
touch "${LOGFILE}"

# Default directories
WASABI_DATASETFILES_DIR="wasabi:gigadb-datasets/dev/pub/10.5524"
S3_DATASETFILES_DIR="gigadb-datasetfiles:gigadb-datasetfiles-backup/dev/pub/10.5524"


# Rclone copy is executed in dry run mode as default. Use --apply flag to turn
# off dry run mode
dry_run=true

# By default, readme files will be copied into dev directory. Use
# --use-live-data flag to copy readme files to live directory
use_live_data=false


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
    --apply)
        dry_run=false
        ;;
    --use-live-data)
        use_live_data=true
        ;;
    *)
        echo "Invalid option: $1"
        exit 1  ## Could be optional.
        ;;
    esac
    shift
done

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

if [[ $(uname -n) =~ compute ]]; then
  if [[ "${use_live_data}" = true ]]; then
    WASABI_DATASETFILES_DIR="wasabi:gigadb-datasets/live/pub/10.5524"
    S3_DATASETFILES_DIR="gigadb-datasetfiles:gigadb-datasetfiles-backup/live/pub/10.5524"
  else
    WASABI_DATASETFILES_DIR="wasabi:gigadb-datasets/staging/pub/10.5524"
    S3_DATASETFILES_DIR="gigadb-datasetfiles:gigadb-datasetfiles-backup/staging/pub/10.5524"
  fi
fi

WASABI_DESTINATION_PATH="${WASABI_DATASETFILES_DIR}/${dir_range}/${doi}"
S3_DESTINATION_PATH="${S3_DATASETFILES_DIR}/${dir_range}/${doi}"

DEV_RCLONE_CONF_LOCATION='../wasabi-migration/config/rclone.conf'

# Construct rclone command to copy readme file to Wasabi
rclone_wasabi_cmd="rclone copy --s3-no-check-bucket ${sourcePath} ${WASABI_DESTINATION_PATH}"
if [[ ! $(uname -n) =~ compute ]];then
  rclone_wasabi_cmd+=" --config ${DEV_RCLONE_CONF_LOCATION}"
fi

if [ "${dry_run}" = true ]; then
  rclone_wasabi_cmd+=" --dry-run"
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
  echo "$(date +'%Y/%m/%d %H:%M:%S') INFO  : Successfully copied file to Wasabi bucket for DOI: $doi" >> "${LOGFILE}"
else
  echo "$(date +'%Y/%m/%d %H:%M:%S') ERROR  : Problem with copying file to Wasabi bucket - rclone has exit code: ${rclone_wasabi_exit_code}" >> "${LOGFILE}"
fi

rclone_s3_cmd="rclone copy --s3-no-check-bucket ${sourcePath} ${S3_DESTINATION_PATH}"
if [[ ! $(uname -n) =~ compute ]];then
  rclone_s3_cmd+=" --config ${DEV_RCLONE_CONF_LOCATION}"
fi

if [ "${dry_run}" = true ]; then
  rclone_s3_cmd+=" --dry-run"
fi
rclone_s3_cmd+=" --log-file ${LOGFILE}"
rclone_s3_cmd+=" --log-level INFO"
rclone_s3_cmd+=" --stats-log-level DEBUG"
rclone_s3_cmd+=" >> ${LOGFILE}"

eval "${rclone_s3_cmd}"
rclone_s3_exit_code=$?
echo "$(date +'%Y/%m/%d %H:%M:%S') INFO  : Executed: ${rclone_s3_cmd}" >> "$LOGFILE"
if [ ${rclone_s3_exit_code} -eq 0 ]; then
  echo "$(date +'%Y/%m/%d %H:%M:%S') INFO  : Successfully copied file to s3 bucket for DOI: $doi" >> "${LOGFILE}"
else
  echo "$(date +'%Y/%m/%d %H:%M:%S') ERROR  : Problem with copying file to s3 buckt - rclone has exit code: ${rclone_s3_exit_code}" >> "${LOGFILE}"
fi