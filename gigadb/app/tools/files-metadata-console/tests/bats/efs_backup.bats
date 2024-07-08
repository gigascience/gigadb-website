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
    run scripts/efs_backup.sh --doi 102468 --sourcePath tests/_data/102480
    [ "$status" -eq 0 ]

    run grep "readme_102480.txt: Skipped copy as --dry-run is set (size 3.127Ki)" ../../uploadDir/backup_"*".log
    run grep "analysis_data/Tree_file.txt: Skipped copy as --dry-run is set (size 359)" ../../uploadDir/backup_"*".log
    run grep "Executed: rclone copy --s3-no-check-bucket tests/_data/102480 wasabi:gigadb-datasets/dev/pub/10.5524/102001_103000/102480" ../../uploadDir/backup_"*".log
    run grep "Successfully copied file to Wasabi bucket for DOI: 102480" ./../uploadDir/backup_"*".log
    run grep "readme_102480.txt: Skipped copy as --dry-run is set (size 3.127Ki)" ../../uploadDir/backup_"*".log
    run grep "analysis_data/Tree_file.txt: Skipped copy as --dry-run is set (size 359)" ../../uploadDir/backup_"*".log
    run grep "Executed:  rclone copy --s3-no-check-bucket tests/_data/102480 gigadb-datasetfiles:gigadb-datasetfiles-backup/dev/pub/10.5524/102001_103000/102480" ../../uploadDir/backup_"*".log
    run grep "Successfully copied file to s3 bucket for DOI: 102480" ../../uploadDir/backup_"*".log
}

@test "With valid DOI in real mode" {
    run scripts/efs_backup.sh --doi 102468 --sourcePath tests/_data/102480 --apply
    [ "$status" -eq 0 ]

    run grep "readme_102480.txt: Copied (new)" ../../uploadDir/backup_"*".log
    run grep "analysis_data/Tree_file.txt: Copied (new)" ../../uploadDir/backup_"*".log
    run grep "Executed: rclone copy --s3-no-check-bucket tests/_data/102480 wasabi:gigadb-datasets/dev/pub/10.5524/102001_103000/102480" ../../uploadDir/backup_"*".log
    run grep "Successfully copied file to Wasabi bucket for DOI: 102480" ./../uploadDir/backup_"*".log ../../uploadDir/backup_"*".log
    run grep "readme_102480.txt: Copied (new)" ../../uploadDir/backup_"*".log
    run grep "analysis_data/Tree_file.txt: Copied (new)" ../../uploadDir/backup_"*".log
    run grep "Executed: rclone copy --s3-no-check-bucket tests/_data/102480 gigadb-datasetfiles:gigadb-datasetfiles-backup/dev/pub/10.5524/102001_103000/102480" ../../uploadDir/backup_"*".log
    run grep "Successfully copied file to s3 bucket for DOI: 102480" ../../uploadDir/backup_"*".log
}