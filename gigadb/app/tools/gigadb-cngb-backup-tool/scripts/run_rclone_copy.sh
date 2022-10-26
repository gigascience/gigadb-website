#!/usr/bin/env bash

# dry run
#time rclone --config=.rclone.conf --dry-run copy --checksum --log-level DEBUG --log-file=tests/test-logs/test_rclone_copy_large_success.log s3genomics:1000genomes/data/NA12878/alignment/ gigaKen:test-transfer-interruption/NA12878
#time rclone --config=.rclone.conf --dry-run copy --checksum --log-level DEBUG --log-file=tests/test-logs/test_rclone_copy_large_change_source_path.log gigaKen:test-transfer-interruption/NA12878  wasabi:test-gigadb-datasets/test-ken-20221010/NA12878
#echo $? > tests/test-logs/test_rclone_status_"$?".log


# real run
#time rclone --config=.rclone.conf copy --checksum --log-level DEBUG --log-file=tests/test-logs/test_rclone_copy_large_success_2nd.log s3genomics:1000genomes/data/NA12878/alignment/ gigaKen:test-transfer-interruption/NA12878
time rclone --config=.rclone.conf copy --checksum --log-level DEBUG --log-file=tests/test-logs/test_rclone_copy_large_change_delete_source.log gigaKen:test-transfer-interruption/NA12878 wasabi:test-gigadb-datasets/test-ken-20221010/NA12878_delete_source
echo $? > tests/test-logs/test_rclone_status_"$?".log