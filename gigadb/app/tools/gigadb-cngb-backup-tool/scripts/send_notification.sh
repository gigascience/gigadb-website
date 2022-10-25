#!/usr/bin/env bash

source .env

token=$GITTER_API_TOKEN
roomId=$GITTER_IT_NOTIFICATION_ROOM_ID
serverName=$(uname -a | cut -f2 -d' ')
date=$(date)

curl -X POST -i -H "Content-Type: application/json" \
      -H "Accept: application/json" \
      -H "Authorization: Bearer $token" "https://api.gitter.im/v1/rooms/$roomId/chatMessages" \
      -d '{"text":"Test Notification from '"$serverName"' at '"$date"':!!!!!!'"$1"'!!!!!!"}'