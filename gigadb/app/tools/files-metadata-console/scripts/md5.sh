#!/bin/bash

# How to use: Place this script in the dataset root directory
# Run ./md5.sh *
pwd=$(pwd)
filename=$(basename $pwd)
find .  -type f -exec md5sum {} \; > "$filename.md5"
