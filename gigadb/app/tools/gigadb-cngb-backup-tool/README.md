# Migrate Gigadb data from CNGB to Wasabi cloud storage


### How to use
```
# Check rclone is installed
% rclone --version
# Generate rclone config file
% ./generate_rclone_config.sh
# Test the connection
% rclone --config=.rclone.conf ls wasabi:gigadb-cngb-backup/test-ken-20221010 
    68096 GigaDBUpload_102203_GIGA-D-21-00197_hamster.xls
      123 logs.md5
% rclone --config=.rclone.conf copy --checksum --log-level DEBUG --log-file=logs/rclone_copy_1.log tests/GigaDBUpload_102203_GIGA-D-21-00197_hamster.xls wasabi:gigadb-cngb-backup/test-ken-20221010
% rclone --config=.rclone.conf ls wasabi:gigadb-cngb-backup/test-ken-20221010
    76288 GigaDBUpload_102203_GIGA-D-21-00197_hamster.xls
      123 logs.md5
% rclone --config=.rclone.conf delete wasabi:gigadb-cngb-backup/test-ken-20221010/logs.md5
% rclone --config=.rclone.conf ls wasabi:gigadb-cngb-backup/test-ken-20221010
    76288 GigaDBUpload_102203_GIGA-D-21-00197_hamster.xls
% rclone --config=.rclone.conf delete wasabi:gigadb-cngb-backup/test-ken-20221010/GigaDBUpload_102203_GIGA-D-21-00197_hamster.xls
```

### Main script
```
# Specify the source dir and source file in the script
# Run the main script
% ./scripts/send_notification.sh
# Then check the log files in logs dir
# And check the dir and files in the bucket
% rclone --config=.rclone.conf ls wasabi:gigadb-cngb-backup/test-ken-20221010                                                    
    76288 GigaDBUpload_102203_GIGA-D-21-00197_hamster.xls
   358912 tortoise/GigaDBUpload-v16_GIGA-D-22-00112-tortoise-102253-2.xls
% rclone --config=.rclone.conf lsd wasabi:gigadb-cngb-backup/test-ken-20221010
           0 2022-10-22 11:33:33        -1 tortoise
# And check the notification message in the gitter channel
```