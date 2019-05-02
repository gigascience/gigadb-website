#!/bin/bash

echo "pure-pw userdel d-$1 -f /etc/pure-ftpd/passwd/pureftpd.passwd -m"
sudo docker exec tus-uppy-proto_ftpd_1 bash -c "pure-pw userdel d-$1 -f /etc/pure-ftpd/passwd/pureftpd.passwd -m"