#!/usr/bin/env bash

# Get the name of the latest migration log file
latestLog=$(ls -Art logs/ | tail -n 1)

# Spin up the swatchdog tool and make it watch the last line of the log
SWATCHDOG_CONFIG="config/swatchdog.conf"
swatchdog -c $SWATCHDOG_CONFIG  --tail-args "-1 -f" -t logs/$latestLog
