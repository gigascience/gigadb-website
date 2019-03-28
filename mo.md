## tusd

docker run -d --name tusd -p 9080:1080 -v /Users/rija/Public/Drop\ Box/:/var/inbox/  -v /Users/rija/Documents/tus-uppy-proto/repo:/var/repo/ -v /Users/rija/Documents/tus-uppy-proto/hooks:/var/hooks rija/tusd -dir /var/inbox -base-path /files/ -hooks-dir /var/hooks


## Pure-FTPd

docker run -d --name ftpd -p 9021:21 -p 30000-30009:30000-30009 -v /Users/rija/Public/Drop\ Box/:/home/ftpusers -v /Users/rija/Documents/tus-uppy-proto/repo:/var/repo/ -v /Users/rija/Documents/tus-uppy-proto/credentials:/var/private -v /Users/rija/Documents/tus-uppy-proto/hooks:/var/scripts -e "PUBLICHOST=localhost" stilliard/pure-ftpd:hardened

docker run -d --name ftpd -p 9021:21 -p 30000-30009:30000-30009 -v /Users/rija/Public/Drop\ Box/:/home/ftpusers -v /Users/rija/Documents/tus-uppy-proto/repo:/var/repo/ -v /Users/rija/Documents/tus-uppy-proto/credentials:/var/private -v /Users/rija/Documents/tus-uppy-proto/hooks:/var/scripts -v /Users/rija/Documents/tus-uppy-proto/run:/var/run -e "PUBLICHOST=localhost" rija/docker-pure-ftpd

docker run -d --name upload-script  -v /Users/rija/Public/Drop\ Box/:/home/ftpusers -v /Users/rija/Documents/tus-uppy-proto/repo:/var/repo/ -v /Users/rija/Documents/tus-uppy-proto/credentials:/var/private -v /Users/rija/Documents/tus-uppy-proto/hooks:/var/scripts -v /Users/rija/Documents/tus-uppy-proto/run:/var/run -e "PUBLICHOST=localhost" rija/docker-pure-ftpd bash -c 'pure-uploadscript -r /var/scripts/move_transfered_files.sh'


### launch post-transfer hook:

docker exec -d ftpd pure-uploadscript -r /var/scripts/move_transfered_files.sh

### create an upload account:

docker exec -it ftpd bash -c 'pure-pw useradd 100006 -f /etc/pure-ftpd/passwd/pureftpd.passwd -m -u ftpuser -d /home/ftpusers/100006  < /var/private/upload_password.txt'

### create a download account:

docker exec -it ftpd bash -c 'pure-pw useradd d100006 -f /etc/pure-ftpd/passwd/pureftpd.passwd -m -u ftpuser -d /var/repo/100006  < /var/private/download_password.txt'