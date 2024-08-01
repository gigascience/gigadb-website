#!/usr/bin/env bats

teardown () {
    echo "executing teardown code"
    FILES="log/backup_"*".log"

    for file in $FILES
    do
      echo "Deleting $file"
      if [ -f "$file" ] ; then
          rm "$file"
      fi
    done

    # Reset the bucket state
    rclone --config ../wasabi-migration/config/rclone.conf delete wasabi:gigadb-datasets/dev/pub/10.5524/102001_103000/102480/
    rclone --config ../wasabi-migration/config/rclone.conf delete gigadb-datasetfiles:gigadb-datasetfiles-backup/dev/pub/10.5524/102001_103000/102480
}

@test "No DOI provided" {
    run scripts/efs_backup.sh
    [ "$status" -eq 1 ]
    [[ "$output" =~ "Usage: scripts/efs_backup.sh --doi <DOI> --sourcePath <Source Path>" ]]
    [[ "$output" =~ "Required:" ]]
    [[ "$output" =~ "--doi            Specify DOI to process" ]]
    [[ "$output" =~ "--sourcePath     Specify source path" ]]
    [[ "$output" =~ "--wasabi         Copy files to Wasabi bucket" ]]
    [[ "$output" =~ "--backup         Copy files to s3 bucket" ]]
    [[ "$output" =~ "Available Option:" ]]
    [[ "$output" =~ "--apply          Escape dry run mode" ]]
    [[ "$output" =~ "Example usages:" ]]
    [[ "$output" =~ "scripts/efs_backup.sh --doi 100148 --sourcePath /share/dropbox/user101 --wasabi" ]]
    [[ "$output" =~ "scripts/efs_backup.sh --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --apply" ]]
    [[ "$output" =~ "scripts/efs_backup.sh --doi 100148 --sourcePath /share/dropbox/user101 --backup" ]]
    [[ "$output" =~ "scripts/efs_backup.sh --doi 100148 --sourcePath /share/dropbox/user101 --backup --apply" ]]
    [[ "$output" =~ "scripts/efs_backup.sh --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --backup" ]]
    [[ "$output" =~ "scripts/efs_backup.sh --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --backup --apply" ]]
}

@test "Show error and usage if no flag" {
    run scripts/efs_backup.sh --doi 102480 --sourcePath tests/_data/102480
    [ "$status" -eq 1 ]
    [ "${lines[0]}" = "Error: please specify --wasabi or --backup or both" ]
    [[ "$output" =~ "Usage: scripts/efs_backup.sh --doi <DOI> --sourcePath <Source Path>" ]]
    [[ "$output" =~ "Required:" ]]
    [[ "$output" =~ "--doi            Specify DOI to process" ]]
    [[ "$output" =~ "--sourcePath     Specify source path" ]]
    [[ "$output" =~ "--wasabi         Copy files to Wasabi bucket" ]]
    [[ "$output" =~ "--backup         Copy files to s3 bucket" ]]
    [[ "$output" =~ "Available Option:" ]]
    [[ "$output" =~ "--apply          Escape dry run mode" ]]
    [[ "$output" =~ "Example usages:" ]]
    [[ "$output" =~ "scripts/efs_backup.sh --doi 100148 --sourcePath /share/dropbox/user101 --wasabi" ]]
    [[ "$output" =~ "scripts/efs_backup.sh --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --apply" ]]
    [[ "$output" =~ "scripts/efs_backup.sh --doi 100148 --sourcePath /share/dropbox/user101 --backup" ]]
    [[ "$output" =~ "scripts/efs_backup.sh --doi 100148 --sourcePath /share/dropbox/user101 --backup --apply" ]]
    [[ "$output" =~ "scripts/efs_backup.sh --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --backup" ]]
    [[ "$output" =~ "scripts/efs_backup.sh --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --backup --apply" ]]

}

@test "Input DOI out of range" {
    run scripts/efs_backup.sh --doi 600700 --sourcePath tests/_data/102480 --wasabi
    [ "$status" -eq 1 ]
    [ "${lines[0]}" = "DOI out of supported range" ]
}

@test "Copy files from dev to Wasabi in dry run mode" {
    run scripts/efs_backup.sh --doi 102480 --sourcePath tests/_data/102480 --wasabi
    [ "$status" -eq 0 ]

    expected_lines=(
        "INFO  : Start copying files from dev to Wasabi"
        "NOTICE: analysis_data/Tree_file.txt: Skipped copy as --dry-run is set (size 359)"
        "NOTICE: readme_102480.txt: Skipped copy as --dry-run is set (size 3.127Ki)"
        "INFO  : Executed: rclone copy --s3-no-check-bucket tests/_data/102480 wasabi:gigadb-datasets/dev/pub/10.5524/102001_103000/102480 --dry-run --config ../wasabi-migration/config/rclone.conf"
        "INFO  : Successfully copied files to Wasabi bucket for DOI: 102480"
    )

    # Check the log
    for line in "${expected_lines[@]}"; do
        run grep -F "$line" log/backup_*.log
        [ "$status" -eq 0 ]
    done

    unexpected_lines=(
        "INFO  : Start copying files from dev to s3"
        "NOTICE: readme_102480.txt: Skipped copy as --dry-run is set (size 3.127Ki)"
        "NOTICE: analysis_data/Tree_file.txt: Skipped copy as --dry-run is set (size 359)"
        "INFO  : Executed: rclone copy --s3-no-check-bucket tests/_data/102480 gigadb-datasetfiles:gigadb-datasetfiles-backup/dev/pub/10.5524/102001_103000/102480 --dry-run --config ../wasabi-migration/config/rclone.conf"
        "INFO  : Successfully copied files to s3 bucket for DOI: 102480"
    )

    # Verify no files copy to s3
    for line in "${unexpected_lines[@]}"; do
        run grep -vF "$line" log/backup_*.log
        [ "$status" -eq 0 ]
    done
}

@test "Copy files from dev to Wasabi with apply flag" {
    run scripts/efs_backup.sh --doi 102480 --sourcePath tests/_data/102480 --wasabi --apply
    [ "$status" -eq 0 ]

    expected_lines=(
        "INFO  : Start copying files from dev to Wasabi"
        "INFO  : analysis_data/Tree_file.txt: Copied (new)"
        "INFO  : readme_102480.txt: Copied (new)"
        "INFO  : Executed: rclone copy --s3-no-check-bucket tests/_data/102480 wasabi:gigadb-datasets/dev/pub/10.5524/102001_103000/102480 --config ../wasabi-migration/config/rclone.conf"
        "INFO  : Successfully copied files to Wasabi bucket for DOI: 102480"
    )

    # Check the log
    for line in "${expected_lines[@]}"; do
        run grep -F "$line" log/backup_*.log
        [ "$status" -eq 0 ]
    done

    # Capture and check the listing from the Wasabi bucket
    run rclone --config ../wasabi-migration/config/rclone.conf ls wasabi:gigadb-datasets/dev/pub/10.5524/102001_103000/102480
    [ "$status" -eq 0 ]
    [[ "$output" =~ "359 analysis_data/Tree_file.txt" ]]
    [[ "$output" =~ "3202 readme_102480.txt" ]]

     # Check no files have been uploaded to s3 bucket that the output is empty
     run rclone --config ../wasabi-migration/config/rclone.conf ls gigadb-datasetfiles:gigadb-datasetfiles-backup/dev/pub/10.5524/102001_103000/102480
     [ -z "$output" ]
}

@test "Copy files from dev to s3 in dry run mode" {
    run scripts/efs_backup.sh --doi 102480 --sourcePath tests/_data/102480 --backup
    [ "$status" -eq 0 ]

    expected_lines=(
        "INFO  : Start copying files from dev to s3"
        "NOTICE: readme_102480.txt: Skipped copy as --dry-run is set (size 3.127Ki)"
        "NOTICE: analysis_data/Tree_file.txt: Skipped copy as --dry-run is set (size 359)"
        "INFO  : Executed: rclone copy --s3-no-check-bucket tests/_data/102480 gigadb-datasetfiles:gigadb-datasetfiles-backup/dev/pub/10.5524/102001_103000/102480 --dry-run --config ../wasabi-migration/config/rclone.conf"
        "INFO  : Successfully copied files to s3 bucket for DOI: 102480"
    )

    # Check the log
    for line in "${expected_lines[@]}"; do
        run grep -F "$line" log/backup_*.log
        [ "$status" -eq 0 ]
    done

    unexpected_lines=(
        "INFO  : Start copying files from dev to Wasabi"
        "INFO  : analysis_data/Tree_file.txt: Copied (new)"
        "INFO  : readme_102480.txt: Copied (new)"
        "INFO  : Executed: rclone copy --s3-no-check-bucket tests/_data/102480 wasabi:gigadb-datasets/dev/pub/10.5524/102001_103000/102480 --config ../wasabi-migration/config/rclone.conf"
        "INFO  : Successfully copied files to Wasabi bucket for DOI: 102480"
    )

    # Verify no copy files to Wasabi
    for line in "${unexpected_lines[@]}"; do
        run grep -vF "$line" log/backup_*.log
        [ "$status" -eq 0 ]
    done

}

@test "Copy files from dev to s3 with apply flag" {
    run scripts/efs_backup.sh --doi 102480 --sourcePath tests/_data/102480 --backup --apply
    [ "$status" -eq 0 ]

    expected_lines=(
        "INFO  : Start copying files from dev to s3"
        "INFO  : analysis_data/Tree_file.txt: Copied (new)"
        "INFO  : readme_102480.txt: Copied (new)"
        "INFO  : Executed: rclone copy --s3-no-check-bucket tests/_data/102480 gigadb-datasetfiles:gigadb-datasetfiles-backup/dev/pub/10.5524/102001_103000/102480 --config ../wasabi-migration/config/rclone.conf"
        "INFO  : Successfully copied files to s3 bucket for DOI: 102480"
    )

    # Check the log
    for line in "${expected_lines[@]}"; do
        run grep -F "$line" log/backup_*.log
        [ "$status" -eq 0 ]
    done

    # Capture and check the listing from the S3 bucket
    run rclone --config ../wasabi-migration/config/rclone.conf ls gigadb-datasetfiles:gigadb-datasetfiles-backup/dev/pub/10.5524/102001_103000/102480
    [ "$status" -eq 0 ]
    [[ "$output" =~ "359 analysis_data/Tree_file.txt" ]]
    [[ "$output" =~ "3202 readme_102480.txt" ]]

    # Check no files have been uploaded to Wasabi bucket that the output is empty
    run rclone --config ../wasabi-migration/config/rclone.conf ls wasabi:gigadb-datasets/dev/pub/10.5524/102001_103000/102480/
    [ -z "$output" ]
}

@test "Copy files from dev to Wasabi and s3 in dry run mode" {
    run scripts/efs_backup.sh --doi 102480 --sourcePath tests/_data/102480 --wasabi --backup
    [ "$status" -eq 0 ]

    expected_lines=(
        "INFO  : Start copying files from dev to Wasabi"
        "NOTICE: analysis_data/Tree_file.txt: Skipped copy as --dry-run is set (size 359)"
        "NOTICE: readme_102480.txt: Skipped copy as --dry-run is set (size 3.127Ki)"
        "INFO  : Executed: rclone copy --s3-no-check-bucket tests/_data/102480 wasabi:gigadb-datasets/dev/pub/10.5524/102001_103000/102480 --dry-run --config ../wasabi-migration/config/rclone.conf"
        "INFO  : Successfully copied files to Wasabi bucket for DOI: 102480"
        "INFO  : Start copying files from dev to s3"
        "NOTICE: readme_102480.txt: Skipped copy as --dry-run is set (size 3.127Ki)"
        "NOTICE: analysis_data/Tree_file.txt: Skipped copy as --dry-run is set (size 359)"
        "INFO  : Executed: rclone copy --s3-no-check-bucket tests/_data/102480 gigadb-datasetfiles:gigadb-datasetfiles-backup/dev/pub/10.5524/102001_103000/102480 --dry-run --config ../wasabi-migration/config/rclone.conf"
        "INFO  : Successfully copied files to s3 bucket for DOI: 102480"
    )

    # Check the log
    for line in "${expected_lines[@]}"; do
        run grep -F "$line" log/backup_*.log
        [ "$status" -eq 0 ]
    done
}

@test "Copy files from dev to Wasabi and s3 and apply flag" {
    run scripts/efs_backup.sh --doi 102480 --sourcePath tests/_data/102480 --wasabi --backup --apply
    [ "$status" -eq 0 ]

     expected_lines=(
        "INFO  : Start copying files from dev to Wasabi"
        "INFO  : analysis_data/Tree_file.txt: Copied (new)"
        "INFO  : readme_102480.txt: Copied (new)"
        "INFO  : Executed: rclone copy --s3-no-check-bucket tests/_data/102480 wasabi:gigadb-datasets/dev/pub/10.5524/102001_103000/102480 --config ../wasabi-migration/config/rclone.conf"
        "INFO  : Successfully copied files to Wasabi bucket for DOI: 102480"
        "INFO  : Start copying files from dev to s3"
        "INFO  : analysis_data/Tree_file.txt: Copied (new)"
        "INFO  : readme_102480.txt: Copied (new)"
        "INFO  : Executed: rclone copy --s3-no-check-bucket tests/_data/102480 gigadb-datasetfiles:gigadb-datasetfiles-backup/dev/pub/10.5524/102001_103000/102480 --config ../wasabi-migration/config/rclone.conf"
        "INFO  : Successfully copied files to s3 bucket for DOI: 102480"
     )

    # Check the log
    for line in "${expected_lines[@]}"; do
        run grep -F "$line" log/backup_*.log
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
}
