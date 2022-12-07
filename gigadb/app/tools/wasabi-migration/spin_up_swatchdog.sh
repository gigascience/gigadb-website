#!/usr/bin/env bash

# Get the name of the latest migration log file, that has been created less than 2 minutes
latestLog=$(find /app/logs/ -type f -mmin -2)

# Spin up the swatchdog tool and make it watch the last line of the log
SWATCHDOG_CONFIG="/app/config/swatchdog.conf"
swatchdog -c $SWATCHDOG_CONFIG  --tail-args "-1 -f" -t $latestLog
