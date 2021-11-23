## tusd

docker run -d --name tusd -p 9080:1080 -v /Users/rija/Public/Drop\ Box/tusd:/var/inbox/  -v /Users/rija/Documents/clients-work/bgi/tus-uppy-proto/repo:/var/repo/ -v /Users/rija/Documents/clients-work/bgi/tus-uppy-proto/hooks:/var/hooks rija/tusd -dir /var/inbox -base-path /files/ -hooks-dir /var/hooks


## Pure-FTPd

docker run -d --name ftpd -p 9021:21 -p 30000-30009:30000-30009 -v /Users/rija/Public/Drop\ Box/ftp:/home/uploader -v /Users/rija/Documents/clients-work/bgi/tus-uppy-proto/repo:/home/downloader/:ro -v /Users/rija/Documents/clients-work/bgi/tus-uppy-proto/credentials:/var/private -v /Users/rija/Documents/clients-work/bgi/tus-uppy-proto/hooks:/var/scripts -v /Users/rija/Documents/clients-work/bgi/tus-uppy-proto/ftp-transfer-logs:/var/log/pure-ftpd -e "PUBLICHOST=localhost" -e "ADDED_FLAGS=-O w3c:/var/log/pure-ftpd/transfer.log" rija/docker-pure-ftpd  -l puredb:/etc/pure-ftpd/pureftpd.pdb -E -j -R -P localhost


### create an upload account:

docker-compose exec ftpd bash -c 'pure-pw useradd u-100006 -f /etc/pure-ftpd/passwd/pureftpd.passwd -m -u uploader -d /home/uploader/100006  < /var/private/upload_password.txt'

ncftpput -u u-100006 -P 9021 -p sfhsadhf localhost / some_file.txt

### create a download account:

docker-compose exec ftpd bash -c 'pure-pw useradd d-100006 -f /etc/pure-ftpd/passwd/pureftpd.passwd -m -u downloader -d /home/downloader/100006  < /var/private/download_password.txt'

ncftpget -u d-100006 -P 9021 -p sfhsadhf localhost . /some_file.txt


### moving files from ftp upload directory to download directory

docker run --name=notify-move -d  -v /Users/rija/Documents/clients-work/bgi/tus-uppy-proto/inotify-config:/config:ro -v /Users/rija/Public/Drop\ Box/ftp:/home/uploader -v /Users/rija/Documents/clients-work/bgi/tus-uppy-proto/repo:/home/downloader/ -v /Users/rija/Documents/clients-work/bgi/tus-uppy-proto/hooks:/commands:ro rija/watchfiles

## add uploaded files to database

* list files [x]
* get filename, file type and file size [x]
* generate ftp link [x]
* update a database table "file" rows for that dataset:
	* update row if columns of a row have changed [x]
	* noop if columns of a row are unchanged [x]

## creating drop box area for a new dataset

* create ftp upload directory [ ]
* create ftp download directory [ ]
* create ftp accounts [ ]
* create an uploader page for dataset [ ]
* create a downloader page for dataset [ ]
* create an admin page [ ]
* setup an nginx container [ ]
