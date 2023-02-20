#!/usr/bin/env bash

source "/app/.secrets"

serverName=$(uname -a | cut -f2 -d' ')
transactionId=`date +%s`
url="/_matrix/client/r0/rooms/$MATRIX_IT_NOTIFICATION_ROOM_ID/send/m.room.message/$transactionId"

# If on backup server then source proxy settings and send notification
if [ "$HOST_HOSTNAME" == "cngb-gigadb-bak" ];
then
  source "/app/proxy_settings.sh" || exit 1

  curl -X PUT -i -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $MATRIX_TOKEN" \
    -d '{"body":"***CNGB BACKUP SERVER*** : '"$1"'", "msgtype":"m.text"}' "${MATRIX_HOMESERVER}${url}"
else
  # Rclone Docker container wants to send Gitter notification message
  curl -X PUT -i -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $MATRIX_TOKEN" \
    -d '{"body":"(Drill) ***rclone.docker*** : '"$1"'", "msgtype":"m.text"}' "${MATRIX_HOMESERVER}${url}"
fi