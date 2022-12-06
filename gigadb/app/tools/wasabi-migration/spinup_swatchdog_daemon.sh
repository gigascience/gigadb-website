#!/usr/bin/env bash

# Get the name of the latest migration log file
latestLog=$(ls -Art logs/ | tail -n 1)

# Spin up swatch daemon
SWATCHDOG_CONFIG="config/swatchdog.conf"
swatchdog -c $SWATCHDOG_CONFIG -t logs/$latestLog --daemon

# Shutdown swatchdog daemon
#ps -ef | grep "migration" | grep -v "grep" | awk '{print $1}' | xargs -r kill -9