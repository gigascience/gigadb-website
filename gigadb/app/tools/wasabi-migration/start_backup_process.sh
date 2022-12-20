#!/usr/bin/env bash

# stop the script upon error
set -e

serverName=$(uname -a | cut -f2 -d' ')

startingDoi=$1
endingDoi=$2
maxBatchSize=$3
useLiveData="false"

devBackupStatus=""
if [ "$serverName" != "cngb-gigadb-bak" ];
then
  if [ "$(docker ps -a | grep -c "wasabi-migration_swatchdog")" -gt 0 ];
  then
    # stop and remove all existing swatchdog service
    docker stop "$(docker ps -aq)" && docker rm "$(docker ps -aq)"
  fi

  # spin up a new swatchdog service
  docker-compose up -d swatchdog

  # start the backup process
  docker-compose run --rm rclone /app/rclone_copy.sh --starting-doi "$startingDoi" --ending-doi "$endingDoi" --max-batch-size "$maxBatchSize"
  devBackupStatus=$?
fi

liveBackupStatus=""
if [ "$serverName" == "cngb-gigadb-bak" ];
then
  if [ "$(docker ps -a | grep -c "wasabi-migration_swatchdog_cngb")" -gt 0 ];
    then
      # stop and remove all existing swatchdog service
      docker stop "$(docker ps -aq)" && docker rm "$(docker ps -aq)"
    fi

  # spin up a new swatchdog service
  docker-compose up -d swatchdog_cngb

  # start the backup process
  if [ "$useLiveData" == "true" ];
  then
    docker-compose run --rm rclone_cngb /app/rclone_copy.sh --starting-doi "$startingDoi" --ending-doi "$endingDoi" --max-batch-size "$maxBatchSize" --use-live-data
    liveBackupStatus=$?
  else
    docker-compose run --rm rclone_cngb /app/rclone_copy.sh --starting-doi "$startingDoi" --ending-doi "$endingDoi" --max-batch-size "$maxBatchSize"
    liveBackupStatus=$?
  fi
fi


# make sure the error message is sent, if any
sleep 5

# house keeping step, stop and remove all existing running services
if [ "$devBackupStatus" -eq 0 ] || [ "$liveBackupStatus" -eq 0 ];
then
  docker stop "$(docker ps -aq)" && docker rm "$(docker ps -aq)"
fi