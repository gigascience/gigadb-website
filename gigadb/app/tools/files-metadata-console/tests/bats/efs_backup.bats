#!/usr/bin/env bats

teardown () {
    echo "executing teardown code"
    FILES="uploadDir/backup_"*".log"

    for file in $FILES
    do
      echo "Deleting $file"
      if [ -f "$file" ] ; then
          rm "$file"
      fi
    done
}

@test "No DOI provided" {
    run scripts/efs_backup.sh
    [ "$status" -eq 1 ]
    [ "${lines[0]}" = "Error: DOI is required!" ]
    [ "${lines[1]}" = "Usage: scripts/efs_backup.sh <DOI> <Source Path>" ]
    [ "${lines[2]}" = "uploads dataset files to the aws s3 bucket - gigadb-datasets-metadata and the wasabi bucket - gigadb-datasets" ]
    [ "${lines[3]}" = "Use scripts/efs_backup.sh <DOI> <Source Path> --apply to escape dry run mode" ]
    [ "${lines[4]}" = "Use scripts/efs_backup.sh <DOI> <Source Path> --use_live_data to upload to live buckets" ]
}

@test "Input out of range DI" {
    run scripts/efs_backup.sh --doi 600700 --sourcePath tests/_data/102480
    [ "$status" -eq 1 ]
    [ "${lines[0]}" = "DOI out of supported range" ]
}

@test "With valid DOI under dry run mode" {
    run scripts/efs_backup.sh --doi 102480 --sourcePath tests/_data/102480
    [ "$status" -eq 0 ]

    expected_lines=(
        "NOTICE: readme_102480.txt: Skipped copy as --dry-run is set (size 3.127Ki)"
        "NOTICE: analysis_data/Tree_file.txt: Skipped copy as --dry-run is set (size 359)"
        "INFO  : Executed: rclone copy --s3-no-check-bucket tests/_data/102480 wasabi:gigadb-datasets/dev/pub/10.5524/102001_103000/102480 --config ../wasabi-migration/config/rclone.conf --dry-run"
        "INFO  : Successfully copied file to Wasabi bucket for DOI: 102480"
        "NOTICE: readme_102480.txt: Skipped copy as --dry-run is set (size 3.127Ki)"
        "NOTICE: analysis_data/Tree_file.txt: Skipped copy as --dry-run is set (size 359)"
        "INFO  : Executed: rclone copy --s3-no-check-bucket tests/_data/102480 gigadb-datasetfiles:gigadb-datasetfiles-backup/dev/pub/10.5524/102001_103000/102480 --config ../wasabi-migration/config/rclone.conf"
        "INFO  : Successfully copied file to s3 bucket for DOI: 102480"
    )

    # Check the log
    for line in "${expected_lines[@]}"; do
        run grep -F "$line" uploadDir/backup_*.log
        [ "$status" -eq 0 ]
    done
}

@test "With valid DOI and apply flag" {
    run scripts/efs_backup.sh --doi 102480 --sourcePath tests/_data/102480 --apply
    [ "$status" -eq 0 ]

     expected_lines=(
        "INFO  : analysis_data/Tree_file.txt: Copied (new)"
        "INFO  : readme_102480.txt: Copied (new)"
        "INFO  : Executed: rclone copy --s3-no-check-bucket tests/_data/102480 wasabi:gigadb-datasets/dev/pub/10.5524/102001_103000/102480 --config ../wasabi-migration/config/rclone.conf"
        "INFO  : Successfully copied file to Wasabi bucket for DOI: 102480"
        "INFO  : analysis_data/Tree_file.txt: Copied (new)"
        "INFO  : readme_102480.txt: Copied (new)"
        "INFO  : Executed: rclone copy --s3-no-check-bucket tests/_data/102480 gigadb-datasetfiles:gigadb-datasetfiles-backup/dev/pub/10.5524/102001_103000/102480 --config ../wasabi-migration/config/rclone.conf"
        "INFO  : Successfully copied file to s3 bucket for DOI: 102480"
     )

    # Check the log
    for line in "${expected_lines[@]}"; do
        run grep -F "$line" uploadDir/backup_*.log
        [ "$status" -eq 0 ]
    done

    # Capture and check the listing from the Wasabi bucket
    run rclone --config ../wasabi-migration/config/rclone.conf ls wasabi:gigadb-datasets/dev/pub/10.5524/102001_103000/102480
    [ "$status" -eq 0 ]
    [[ "$output" =~ "359 analysis_data/Tree_file.txt" ]]
    [[ "$output" =~ "3202 readme_102480.txt" ]]

    # Capture and check the listing from the S3 bucket
    run rclone --config ../wasabi-migration/config/rclone.conf ls gigadb-datasetfiles:gigadb-datasetfiles-backup/dev/pub/10.5524/102001_103000/102480
    [ "$status" -eq 0 ]
    [[ "$output" =~ "359 analysis_data/Tree_file.txt" ]]
    [[ "$output" =~ "3202 readme_102480.txt" ]]

    # Reset the bucket state
    run rclone --config ../wasabi-migration/config/rclone.conf delete wasabi:gigadb-datasets/dev/pub/10.5524/102001_103000/102480/ -v
    [[ "$output" =~ "INFO  : readme_102480.txt: Deleted" ]]
    [[ "$output" =~ "INFO  : analysis_data/Tree_file.txt: Deleted" ]]

    run rclone --config ../wasabi-migration/config/rclone.conf delete gigadb-datasetfiles:gigadb-datasetfiles-backup/dev/pub/10.5524/102001_103000/102480 -v
    [[ "$output" =~ "INFO  : readme_102480.txt: Deleted" ]]
    [[ "$output" =~ "INFO  : analysis_data/Tree_file.txt: Deleted" ]]
}

