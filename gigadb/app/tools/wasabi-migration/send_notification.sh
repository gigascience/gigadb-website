#!/usr/bin/env bash

source "/app/.secrets"

serverName=$(uname -a | cut -f2 -d' ')

# If on backup server then source proxy settings and send notification
if [ "$HOST_HOSTNAME" == "cngb-gigadb-bak" ];
then
  source "/app/proxy_settings.sh" || exit 1

  curl -X POST -i -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $GITTER_API_TOKEN" "https://api.gitter.im/v1/rooms/$GITTER_IT_NOTIFICATION_ROOM_ID/chatMessages" \
    -d '{"text":"***CNGB BACKUP SERVER*** : '"$1"'"}'
else
  # Rclone Docker container wants to send Gitter notification message
  curl -X POST -i -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $GITTER_API_TOKEN" "https://api.gitter.im/v1/rooms/$GITTER_IT_NOTIFICATION_ROOM_ID/chatMessages" \
    -d '{"text":"(Drill) ***rclone.docker*** : '"$1"'"}'
fi
