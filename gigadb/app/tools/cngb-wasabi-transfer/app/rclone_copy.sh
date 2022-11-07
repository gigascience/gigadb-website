#!/usr/bin/env bash

# Bail out upon error
set -e

# Allow all scripts to base include, log, etc. paths off the
# directory where backup script is located
APP_HOME=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )

# Setup logging
LOGDIR="$APP_HOME/logs"
LOGFILE="$LOGDIR/transfer_$(date +'%Y%m%d_%H%M%S').log"
mkdir -p $LOGDIR
touch $LOGFILE

# Include proxy settings to perform data transfer
#source "$APP_HOME/proxy_settings.sh" || exit 1

# Parse command line parameters
#starting_doi=""
#ending_doi=""

while [[ $# -gt 0 ]]; do
    case "$1" in
    --starting-doi)
            has_starting_doi=true
            starting_doi=$2
            shift
            ;;
    --ending-doi)
            has_ending_doi=true
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

echo "$starting_doi"
echo "$ending_doi"

# Determine which DOI directory range to use
dir_range=""
if [ "$starting_doi" -lt 101000 ];
then
    dir_range="100001_101000"
elif [ "$starting_doi" -lt 102000 ] && [ "$starting_doi" -gt 101001 ]; 
then
    dir_range="101001_102000"
elif [ "$starting_doi" -lt 103000 ] && [ "$starting_doi" -gt 102001 ]; 
then
    dir_range="102001_103000"
fi

# Copy dataset files for all DOIs between starting and ending DOIs
current_doi="$starting_doi"
while [ "$current_doi" -lt "$ending_doi" ] || [ "$current_doi" -eq "$ending_doi" ]
do
#    echo "Current DOI: $current_doi"
  
    # Create directory paths
    source_path="${DATASETS_PATH}${dir_range}/${current_doi}"
    destination_path="/gigadb_datasets/${dir_range}/${current_doi}"
    
    # Check directory for current DOI exists
    if [ -d "$source_path" ]; then
      echo "$source_path exists"
      
      # Perform data transfer to Wasabi
      #    rclone copy $source_path $destination_path \
      #        --log-file=$LOGFILE \
      #        --log-level INFO \
      #        --stats-log-level DEBUG >> $LOGFILE;
      #    
      #    # Check exit code for rclone command
      #    if [ $? -eq 0 ] 
      #    then 
      #      echo "Successfully executed rclone copy" 
      #    else 
      #      echo "Problem with copying files to Wasabi by rclone: " >&2 
      #    fi
    fi
    
    # Increment current DOI
    ((current_doi=current_doi+1))
done


