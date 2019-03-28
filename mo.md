## tusd

docker run -d --name tusd -p 9080:1080 -v /Users/rija/Public/Drop\ Box/:/var/inbox/  -v /Users/rija/Documents/tus-uppy-proto/repo:/var/repo/ -v /Users/rija/Documents/tus-uppy-proto/hooks:/var/hooks rija/tusd -dir /var/inbox -base-path /files/ -hooks-dir /var/hooks


## Pure-FTPd

docker run -d --name ftpd -p 9021:21 -p 30000-30009:30000-30009 -v /Users/rija/Public/Drop\ Box/:/home/ftpusers -v /Users/rija/Documents/tus-uppy-proto/repo:/var/repo/ -v /Users/rija/Documents/tus-uppy-proto/credentials:/var/private -v /Users/rija/Documents/tus-uppy-proto/hooks:/var/scripts -e "PUBLICHOST=localhost" stilliard/pure-ftpd:hardened

docker run -d --name ftpd -p 9021:21 -p 30000-30009:30000-30009 -v /Users/rija/Public/Drop\ Box/:/home/uploader -v /Users/rija/Documents/clients-work/bgi/tus-uppy-proto/repo:/home/downloader/ -v /Users/rija/Documents/clients-work/bgi/tus-uppy-proto/credentials:/var/private -v /Users/rija/Documents/clients-work/bgi/tus-uppy-proto/hooks:/var/scripts -v /Users/rija/Documents/clients-work/bgi/tus-uppy-proto/ftp-transfer-logs:/var/log/pure-ftpd -e "PUBLICHOST=localhost" -e "ADDED_FLAGS=-O w3c:/var/log/pure-ftpd/transfer.log" rija/docker-pure-ftpd bash -c '/run.sh -l puredb:/etc/pure-ftpd/pureftpd.pdb -E -j -R -P $PUBLICHOST'


### create an upload account:

docker exec -it ftpd bash -c 'pure-pw useradd u-100006 -f /etc/pure-ftpd/passwd/pureftpd.passwd -m -u uploader -d /home/uploader/100006  < /var/private/upload_password.txt'

ncftpput -u u-100006 -P 9021 -p sfhsadhf localhost / some_file.txt

### create a download account:

docker exec -it ftpd bash -c 'pure-pw useradd d-100006 -f /etc/pure-ftpd/passwd/pureftpd.passwd -m -u downloader -d /home/downloader/100006  < /var/private/download_password.txt'

ncftpget -u d-100006 -P 9021 -p sfhsadhf localhost . /some_file.txt