#!/usr/bin/env bats

teardown () {
    echo "executing teardown code"
    FILES="gigadb/app/tools/readme-generator/runtime/curators/readme_100020.txt
    gigadb/app/tools/readme-generator/logs/readme_100020_$(date +'%Y%m%d').log"

    for file in $FILES
        do
          echo "Deleting $file"
          if [ -f "$file" ] ; then
              rm "$file"
          fi
        done
}

@test "display help message with help option" {
    run ./gigadb/app/tools/excel-spreadsheet-uploader/postUpload.sh --help
    [ "$status" -eq 0 ]
    [ "${lines[0]}" = "Usage: ./gigadb/app/tools/excel-spreadsheet-uploader/postUpload.sh [Options]" ]
    [ "${lines[1]}" = "Options:" ]
    [ "${lines[2]}" = "  --doi <value>           Specify the DOI value" ]
    [ "${lines[3]}" = "  --wasabi                (Optional) Copy the readme file to wasabi bucket in dry-run mode" ]
    [ "${lines[4]}" = "  --apply                 (Optional) Copy the readme file to wasabi non live bucket" ]
    [ "${lines[5]}" = "  --use-live-data         (Optional) Copy the readme file to wasabi live bucket" ]
    [ "${lines[6]}" = "  -h, --help              Display this help message" ]
}

@test "display help message without any option" {
    run ./gigadb/app/tools/excel-spreadsheet-uploader/postUpload.sh
    [ "$status" -eq 1 ]
    [ "${lines[0]}" = "Usage: ./gigadb/app/tools/excel-spreadsheet-uploader/postUpload.sh [Options]" ]
    [ "${lines[1]}" = "Options:" ]
    [ "${lines[2]}" = "  --doi <value>           Specify the DOI value" ]
    [ "${lines[3]}" = "  --wasabi                (Optional) Copy the readme file to wasabi bucket in dry-run mode" ]
    [ "${lines[4]}" = "  --apply                 (Optional) Copy the readme file to wasabi non live bucket" ]
    [ "${lines[5]}" = "  --use-live-data         (Optional) Copy the readme file to wasabi live bucket" ]
    [ "${lines[6]}" = "  -h, --help              Display this help message" ]
}

@test "create readme file and rclone log" {
    run ./gigadb/app/tools/excel-spreadsheet-uploader/postUpload.sh --doi 100020
    # Check readme file has been created
    [ -f gigadb/app/tools/readme-generator/runtime/curators/readme_100020.txt ]
     # check rclone log has been created
    [ -f gigadb/app/tools/readme-generator/logs/readme_100020_$(date +'%Y%m%d').log ]
}