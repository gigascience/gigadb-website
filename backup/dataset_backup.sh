export backup_date=`date "+%Y%m%d"`

/usr/bin/coscmd -l /home/gigadb/test/giga_cosbackup.log$backup_date upload -H '{"x-cos-storage-class":"DEEP_ARCHIVE"}' -rs /data/gigadb/pub/10.5524/100001_101000/100905 /cngbdb/
giga/gigadb/pub/10.5524/100001_101000/

