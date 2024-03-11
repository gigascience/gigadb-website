#!/bin/bash

# How to use: Place this script in the dataset root directory
# Run ./filesizes.sh *
pwd=$(pwd)
filename=$(basename $pwd)
for i in $(find .  -type f);
do
  echo "$i"
  stat -f '%z' "$i" | numfmt --to=iec --format=%.2f
  echo -e "$(stat -f '%z' "$i" | numfmt --to=iec --format=%.2f)\t$i" >> "$filename.tsv"
done
