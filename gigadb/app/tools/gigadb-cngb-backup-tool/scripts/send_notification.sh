#!/usr/bin/env bash

source .env

token=$GITTER_API_TOKEN
roomId=$GITTER_IT_NOTIFICATION_ROOM_ID
serverName=$(uname -a | cut -f2 -d' ')
date=$(date)
sourceDir=tests
sourceFile=/GigaDBUpload_102203_GIGA-D-21-00197_hamster.xls
backUpDir=wasabi:gigadb-cngb-backup/test-ken-20221010


rclone --config=.rclone.conf copy --checksum --log-level DEBUG --log-file=logs/rclone_copy_1.log $sourceDir $backUpDir
exitCode_1=$?
if [[ $exitCode_1 -gt 0 ]]
then
    curl -X POST -i -H "Content-Type: application/json" \
            -H "Accept: application/json" \
            -H "Authorization: Bearer $token" "https://api.gitter.im/v1/rooms/$roomId/chatMessages" \
            -d '{"text":"Test Notification from '"$serverName"' at '"$date"': Test Upload '"$sourceDir"' failed with exit code '"$exitCode_1"', retry with option --s3-no-check-bucket"}'
    rclone --config=.rclone.conf copy --checksum --log-level DEBUG --log-file=logs/rclone_copy_2.log --s3-no-check-bucket $sourceDir $backUpDir
    exitCode_2=$?
    if [[ exitCode_2 -gt 0 ]]
    then
            curl -X POST -i -H "Content-Type: application/json" \
                    -H "Accept: application/json" \
                    -H "Authorization: Bearer $token" "https://api.gitter.im/v1/rooms/$roomId/chatMessages" \
                    -d '{"text":"Test Notification from '"$serverName"' at '"$date"': Test Upload '"$sourceDir"' still failed with exit code '"$exitCode_2"'!!!"}'
    else
            curl -X POST -i -H "Content-Type: application/json" \
                -H "Accept: application/json" \
                -H "Authorization: Bearer $token" "https://api.gitter.im/v1/rooms/$roomId/chatMessages" \
                -d '{"text":"Test Notification from '"$serverName"' at '"$date"': ***Test Upload '"$sourceDir"' Completed***"}'
    fi
else
        curl -X POST -i -H "Content-Type: application/json" \
                -H "Accept: application/json" \
                -H "Authorization: Bearer $token" "https://api.gitter.im/v1/rooms/$roomId/chatMessages" \
                -d '{"text":"Test Notification from '"$serverName"' at '"$date"': !!!Test Upload '"$sourceDir"' Completed!!!"}'
fi