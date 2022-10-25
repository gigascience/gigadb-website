# Migrate Gigadb data from CNGB to Wasabi cloud storage


### How to use
```
# Check rclone is installed
% rclone --version
# Generate rclone config file
% ./generate_rclone_config.sh
# Test the connection
% rclone --config=.rclone.conf ls wasabi:test-gigadb-datasets/test-ken-20221010 
    68096 GigaDBUpload_102203_GIGA-D-21-00197_hamster.xls
      123 logs.md5
% rclone --config=.rclone.conf copy --checksum --log-level DEBUG --log-file=logs/rclone_copy_1.log tests/GigaDBUpload_102203_GIGA-D-21-00197_hamster.xls wasabi:test-gigadb-datasets/test-ken-20221010
% rclone --config=.rclone.conf ls wasabi:gigadb-cngb-backup/test-ken-20221010
    76288 GigaDBUpload_102203_GIGA-D-21-00197_hamster.xls
      123 logs.md5
% rclone --config=.rclone.conf delete wasabi:test-gigadb-datasets/test-ken-20221010/logs.md5
% rclone --config=.rclone.conf ls wasabi:test-gigadb-datasets/test-ken-20221010
    76288 GigaDBUpload_102203_GIGA-D-21-00197_hamster.xls
% rclone --config=.rclone.conf delete wasabi:test-gigadb-datasets/test-ken-20221010/GigaDBUpload_102203_GIGA-D-21-00197_hamster.xls
```

### Main script
```
# Run the log parser script, by appending the suitable log
% ./scripts/parse_rclone_log.sh rclone.log
# And check the notification message in the gitter channel
```