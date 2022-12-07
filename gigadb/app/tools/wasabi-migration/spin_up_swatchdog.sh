#!/usr/bin/env bash

# Get the name of the latest migration log file
latestLog=$(ls -Art /app/logs/ | tail -n 1)

# Spin up the swatchdog tool and make it watch the last line of the log
SWATCHDOG_CONFIG="/app/config/swatchdog.conf"
swatchdog -c $SWATCHDOG_CONFIG  --tail-args "-1 -f" -t /app/logs/$latestLog
