export backup_date=`date "+%Y%m%d"`

/usr/bin/coscmd -l /home/gigadb/giga_cosbackup.log$backup_date upload -H '{"x-cos-storage-class":"DEEP_ARCHIVE"}'  --skipmd5 -rs /data/gigadb/pub /cngbdb/giga/gigadb/
