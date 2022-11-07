#!/usr/bin/env bash

# Bail out upon error
set -e

# Allow all scripts to base include, log, etc. paths off the
# directory where backup script is located
PROJECT_HOME="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Setup logging
LOGDIR="$PROJECT_HOME/logs"
LOGFILE="$LOGDIR/transfer_$(date +'%Y%m%d_%H%M%S').log"
mkdir -p $LOGDIR
touch $LOGFILE

# Include proxy settings to perform data transfer
#source "$PROJECT_HOME/proxy_settings.sh" || exit 1

# Parse command line parameters
starting_doi=""
ending_doi=""

while [[ $# -gt 0 ]]; do
    case "$1" in
    --starting-doi)
            starting_doi=$1
            shift
            ;;
    --ending-doi)
            ending_doi=$2
            shift
            ;;
    *)
        echo "Invalid option: $1"
        exit 1  ## Could be optional.
        ;;
    esac
    shift
done

# Variables for creating directory paths
DATASETS_PATH="/data/gigadb/pub/10.5524/"

# Determine which DOI directory range to use
dir_range=""
if [ $1 -lt 101000 ]
then
    dir_range="100001_101000"
elif [ $1 -lt 102000 ] && [ $1 -gt 101001 ]; 
then
    dir_range="101001_102000"
elif [ $1 -lt 103000 ] && [ $1 -gt 102001 ]; 
then
    dir_range="102001_103000"
fi

# Copy dataset files for all DOIs between starting and ending DOIs
current_doi=starting_doi
while [ "$current_doi" -lt "$ending_doi" ] || [ "$current_doi" -eq "$ending_doi" ]
do
    # Create directory paths
    source_path="${DATASETS_PATH}${dir_range}/${starting_doi}"
    destination_path="/gigadb_datasets/${dir_range}/${starting_doi}"
    
    # Check directory for current DOI exists
    
    # Perform data transfer to Wasabi
    rclone copy $source_path $destination_path \
        --log-file=$LOGFILE \
        --log-level INFO \
        --stats-log-level DEBUG >> $LOGFILE;
    
    # Check exit code for rclone command
    if [ $? -eq 0 ] 
    then 
      echo "Successfully executed rclone copy" 
    else 
      echo "Problem with copying files to Wasabi by rclone: " >&2 
    fi
    
    # Increment current DOI
    ((current_doi=current_doi+1))
done


