#!/usr/bin/env bash

# Assign the name of the latest migration log file
logFile="/app/logs/rclone_latest.log"

# Spin up the swatchdog tool and make it watch the last line of the log
SWATCHDOG_CONFIG="/app/config/swatchdog.conf"
swatchdog -c $SWATCHDOG_CONFIG  --tail-args "-1 -f" -t $logFile