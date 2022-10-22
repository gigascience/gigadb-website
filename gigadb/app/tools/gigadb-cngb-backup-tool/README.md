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
```