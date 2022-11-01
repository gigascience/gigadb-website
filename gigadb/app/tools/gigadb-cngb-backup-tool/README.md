# Migrate Gigadb data from CNGB to Wasabi cloud storage


### How to use
```
# Go the working dir
cd gigadb/app/tools/gigadb-cngb-backup-tool
# Generate rclone config file
% ./generate_rclone_config.sh
# Spin up the tool
% docker image build -q -t gigadb-alpine .
% docker run --rm -it gigadb-alpine 
# Test the connection
/gigadb-cngb-backup-tool # ls
Dockerfile                 config-source              logs                       tests
README.md                  generate_rclone_config.sh  scripts
/gigadb-cngb-backup-tool # rclone --version
rclone v1.60.0
- os/version: alpine 3.16.2 (64 bit)
- os/kernel: 5.10.47-linuxkit (x86_64)
- os/type: linux
- os/arch: amd64
- go/version: go1.19.2
- go/linking: static
- go/tags: none
/gigadb-cngb-backup-tool # rclone --config=.rclone.conf lsd wasabi:test-gigadb-datasets
           0 2022-11-01 08:04:54        -1 peter
           0 2022-11-01 08:04:54        -1 test-ken-20221010
/gigadb-cngb-backup-tool # rclone --config=.rclone.conf copy --checksum --log-level DEBUG --log-file=logs/rclone_copy_1.log tests/GigaDBUpload_102203_GIGA-D-21-00197_hamster.xls wasabi:test-gigadb-datasets/test-ken-20221010
# Start the swatchdog tool in background
/gigadb-cngb-backup-tool # swatchdog -c swatchdog/swatchdog.conf -t logs/rclone_copy_change_policy.log --daemon
/gigadb-cngb-backup-tool # rclone --config=.rclone.conf copy --checksum --log-level DEBUG --s3-no-check-bucket --log-file=logs/rclone_copy_change_policy.log s3genomics:1000genomes/data/NA12878/a
lignment/NA12878.alt_bwamem_GRCh38DH.20150718.CEU.low_coverage.bam.bas wasabi:test-gigadb-datasets/test-ken-20221010/folder_to_delete
# swatchdog will have this cmd line output, and will pass this line to send_notification.sh, finally check this message in gitter notification channel
/gigadb-cngb-backup-tool # 2022/11/01 08:35:37 INFO  : NA12878.alt_bwamem_GRCh38DH.20150718.CEU.low_coverage.bam.bas: Copied (new)
```
