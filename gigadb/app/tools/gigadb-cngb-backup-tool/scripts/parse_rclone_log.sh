#!/usr/bin/env bash

logFileName=$1

while read line;
do
# output the line containing ERROR or INFO
  if [[ $line =~ "ERROR" || $line =~ "INFO" ]]
  then
    ./scripts/send_notification.sh "$line"
  fi
done < $logFileName