#!/usr/bin/env bash

set -eu

hostname=${1:-"academic.oup.com"}
date=$(echo | openssl s_client -connect $hostname:443 2>/dev/null | openssl x509 -noout -enddate | sed "s/.*=\(.*\)/\1/")
date_s=$(date -d "${date}" +%s)
now_s=$(date -d now +%s)
date_diff=$(( (date_s - now_s) / 86400 ))

if [[ "$date_diff" -gt "1" && "$date_diff" -lt "11" ]]; then
  echo "Certificate expires in: $date_diff days"
  exit 1
elif [[ "$date_diff" -gt "10" ]]; then
  echo "Certificate expires in: $date_diff days"
  exit 0
else
  echo "hostname does not use SSL Certificates"
  exit 0
fi