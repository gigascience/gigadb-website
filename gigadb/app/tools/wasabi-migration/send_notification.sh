#!/usr/bin/env bash

source /app/.secrets

serverName=$(uname -a | cut -f2 -d' ')
date=$(date)

curl -X POST -i -H "Content-Type: application/json" \
      -H "Accept: application/json" \
      -H "Authorization: Bearer $GITTER_API_TOKEN" "https://api.gitter.im/v1/rooms/$GITTER_IT_NOTIFICATION_ROOM_ID/chatMessages" \
      -d '{"text":"From server ***'"$serverName"'*** : '"$1"'"}'