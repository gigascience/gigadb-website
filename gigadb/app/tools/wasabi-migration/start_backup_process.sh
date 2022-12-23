#!/usr/bin/env bash

# stop the script upon error
set -e

serverName=$(uname -a | cut -f2 -d' ')

startingDoi=$1
endingDoi=$2
maxBatchSize=$3
useLiveData="false"

devBackupStatus=""
if [ "$serverName" != "cngb-gigadb-bak" ]; then
  if [ "$(docker-compose ps | grep "wasabi-migration_swatchdog" | grep -c "Up")" -gt 0 ]; then
    # stop existing swatchdog service, if any
    docker-compose stop swatchdog || true
  fi

  # spin up a new swatchdog service
  docker-compose up -d swatchdog

  # start the backup process
  docker-compose run --rm rclone /app/rclone_copy.sh --starting-doi "$startingDoi" --ending-doi "$endingDoi" --max-batch-size "$maxBatchSize"
  devBackupStatus=$?
fi

liveBackupStatus=""
if [ "$serverName" == "cngb-gigadb-bak" ]; then
  if [ "$(docker-compose ps | grep "wasabi-migration_swatchdog_cngb" | grep -c "Up")" -gt 0 ]; then
      # stop existing swatchdog service, if any
      docker-compose stop swatchdog || true
    fi

  # spin up a new swatchdog service
  docker-compose up -d swatchdog_cngb

  # start the backup process
  if [ "$useLiveData" == "true" ]; then
    docker-compose run --rm rclone_cngb /app/rclone_copy.sh --starting-doi "$startingDoi" --ending-doi "$endingDoi" --max-batch-size "$maxBatchSize" --use-live-data
    liveBackupStatus=$?
  else
    docker-compose run --rm rclone_cngb /app/rclone_copy.sh --starting-doi "$startingDoi" --ending-doi "$endingDoi" --max-batch-size "$maxBatchSize"
    liveBackupStatus=$?
  fi
fi


# make sure the error message is sent, if any
sleep 5

# house keeping step, stop swatchdog service
# if the status in not empty and the backup process success
if [[ -n ${devBackupStatus+x} && $devBackupStatus -eq 0 ]]; then
  docker-compose stop swatchdog
fi

if [[ -n ${liveBackupStatus+x} && $liveBackupStatus -eq 0 ]]; then
  docker-compose stop swatchdog_cngb
fi