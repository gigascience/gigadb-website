#!/usr/bin/env bash
#
# Create readme file and optionally upload to Wasabi

# Stop script upon error
set -e

PATH=/usr/local/bin:$PATH
export PATH

# Locations of rclone.conf
BASTION_RCLONE_CONF_LOCATION=' --config /home/centos/.config/rclone/rclone.conf'
DEV_RCLONE_CONF_LOCATION=' --config ../wasabi-migration/config/rclone.conf'

# Rclone copy is executed in dry run mode as default
# Use --apply flag to turn off dry run mode
dry_run=true

# By default, readme files will be copied into the
# dev directory. Use the --use-live-data flag to
# readme files to the live directory
use_live_data=false

# Parse command line parameters
while [[ $# -gt 0 ]]; do
    case "$1" in
    --doi)
        doi=$2
        shift
        ;;
    --outdir)
        outdir=$2
        shift
        ;;
    --wasabi)
        wasabi_upload=true
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

# Allow all scripts to base themselves from the directory where backup script 
# is located
APP_SOURCE=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )

# Setup logging
LOGDIR="$APP_SOURCE/logs"
LOGFILE="$LOGDIR/wasabi_${doi}_$(date +'%Y%m%d_%H%M%S').log"
mkdir -p "${LOGDIR}"
touch "${LOGFILE}"

# Default is to copy readme file to dev directory in Wasabi
SOURCE_PATH="${APP_SOURCE}/runtime/curators"
if [ "${use_live_data}" = false ]; then
  DESTINATION_PATH="wasabi:gigadb-datasets/dev/pub/10.5524"
elif [ "${use_live_data}" = true ]; then
  echo "$(date +'%Y/%m/%d %H:%M:%S') INFO  : Copying readme file into LIVE data directory" >> "$LOGFILE"
  DESTINATION_PATH="wasabi:gigadb-datasets/live/pub/10.5524"
fi

#######################################
# Determine DOI range directory name
# Globals:
#   dir_range
#   doi
# Arguments:
#   None
#######################################
function get_doi_directory_range() {
  if [ "${doi}" -le 101000 ]; then
    dir_range="100001_101000"
  elif [ "${doi}" -le 102000 ] && [ "${doi}" -ge 101001 ]; then
    dir_range="101001_102000"
  elif [ "${doi}" -le 103000 ] && [ "${doi}" -ge 102001 ]; then
    dir_range="102001_103000"
  fi
}

#######################################
# Copies readme text file into Wasabi bucket
# Globals:
#   source_dataset_path
#   destination_dataset_path
#   SOURCE_PATH
#   doi
#   DESTINATION_PATH
#   dir_range
#   LOGFILE
#   rclone_exit_code
# Arguments:
#   None
#######################################
function copy_to_wasabi() {
  # Create directory path to datasets
  source_dataset_path="${SOURCE_PATH}/readme_${doi}.txt"
  destination_dataset_path="${DESTINATION_PATH}/${dir_range}/${doi}/"

  # Check readme file exists
  if [ -f "$source_dataset_path" ]; then
    # Continue running script if there is an error executing rclone copy
    set +e
    # Construct rclone command to copy readme file to Wasabi
    rclone_cmd="rclone copy ${source_dataset_path} ${destination_dataset_path}"
    if [[ $(uname -n) =~ compute ]];then
      rclone_cmd+="${BASTION_RCLONE_CONF_LOCATION}"
    else
      rclone_cmd+="${DEV_RCLONE_CONF_LOCATION}"
    fi
    rclone_cmd+=" --log-file=${LOGFILE}"
    rclone_cmd+=" --log-level INFO"
    rclone_cmd+=" --stats-log-level DEBUG"
    if [ "${dry_run}" = true ]; then
      rclone_cmd+=" --dry-run"
    fi
    rclone_cmd+=" >> ${LOGFILE}"
    echo "$(date +'%Y/%m/%d %H:%M:%S') INFO  : Executing: ${rclone_cmd}" >> "$LOGFILE"
    # Execute command
    eval "${rclone_cmd}"
    # Check exit code for rclone command
    rclone_exit_code=$?
    if [ $rclone_exit_code -eq 0 ]; then
      echo "$(date +'%Y/%m/%d %H:%M:%S') INFO  : Successfully copied file to Wasabi for DOI: $doi" >> "$LOGFILE"
    else 
      echo "$(date +'%Y/%m/%d %H:%M:%S') ERROR  : Problem with copying file to Wasabi - rclone has exit code: $rclone_exit_code" >> "$LOGFILE"
    fi
  else
    echo "$(date +'%Y/%m/%d %H:%M:%S') DEBUG  : Could not find file $source_dataset_path" >> "$LOGFILE"
  fi
}

# Conditional for how to generate readme file - dependant on user's environment
if [[ $(uname -n) =~ compute ]];then
  . /home/centos/.bash_profile
  docker run --rm -v /home/centos/readmeFiles:/app/readmeFiles registry.gitlab.com/$GITLAB_PROJECT/production_tool:$GIGADB_ENV /app/yii readme/create --doi "$doi" --outdir "$outdir"
else
  docker-compose run --rm tool /app/yii readme/create --doi "$doi" --outdir "$outdir"
fi

# Readme file can be copied into Wasabi if --wasabi flag is present
if [ "$wasabi_upload" ]; then
  dir_range=""
  get_doi_directory_range
  copy_to_wasabi
fi
