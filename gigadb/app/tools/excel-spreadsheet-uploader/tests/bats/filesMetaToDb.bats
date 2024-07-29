#!/usr/bin/env bats

teardown () {
    echo "executing teardown code"
        FILES="../readme-generator/runtime/curators/updating-file-size-100006.txt
        ../readme-generator/runtime/curators/updating-md5checksum-100006.txt
        ../readme-generator/runtime/curators/updating-file-size-100020.txt
        ../readme-generator/runtime/curators/updating-md5checksum-100020.txt
        ../readme-generator/runtime/curators/updating-file-size-888888.txt
        ../readme-generator/runtime/curators/updating-md5checksum-888888.txt"

        for file in $FILES
        do
          echo "Deleting $file"
          if [ -f "$file" ] ; then
              rm "$file"
          fi
        done
}


@test "No DOI provided" {
    run ./filesMetaToDb.sh
    [ "$status" -eq 1 ]
    [ "${lines[0]}" = "Usage: ./filesMetaToDb.sh <DOI>" ]
}

@test "DOI with existing filesize file and md5 file in s3 bucket" {
    run ./filesMetaToDb.sh 100006
    [[ "$output" =~ "* About to update files' MD5 Checksum as file attribute for 100006" ]]
    [[ "$output" =~ "Saved md5 file attribute with id: 10674" ]]
    [[ "$output" =~ "Saved md5 file attribute with id: 10672" ]]
    [[ "$output" =~ "Saved md5 file attribute with id: 10673" ]]
    [[ "$output" =~ "Saved md5 file attribute with id: 10671" ]]
    [[ "$output" =~ "Saved md5 file attribute with id: 10670" ]]
    [[ "$output" =~ "Saved md5 file attribute with id: 10669" ]]
    [[ "$output" =~ "Saved md5 file attribute with id: 10675" ]]
    [[ "$output" =~ "Done with updating files' MD5 Checksum as file attribute for 100006. Process status is saved in file: /home/curators/updating-md5checksum-100006.txt" ]]
    [[ "$output" =~ "* About to update files' size for 100006" ]]
    [[ "$output" =~ "Number of changes: 6" ]]
    [[ "$output" =~ "Done with updating files' size for 100006. Nb of successful changes saved in file: /home/curators/updating-file-size-100006.txt" ]]

    [ -f "../readme-generator/runtime/curators/updating-file-size-100006.txt" ]
    [ -f "../readme-generator/runtime/curators/updating-md5checksum-100006.txt" ]
}

@test "DOI with non-existing md5 file in s3 bucket" {
    # DOI 100020 has no 100020.md5 in the s3 gigadb-dataset-metadata bucket
    run ./filesMetaToDb.sh 100020
    [[ "$output" =~ "* About to update files' MD5 Checksum as file attribute for 100020" ]]
    [[ "$output" =~ "No 100020.md5 file could be found for dataset DOI 100020" ]]
    [[ "$output" =~ "Exception 'Exception' with message 'https://s3.ap-northeast-1.amazonaws.com/gigadb-datasets-metadata/100020.filesizes not found'" ]]

    [ -f "../readme-generator/runtime/curators/updating-file-size-100020.txt" ]
    [ -f "../readme-generator/runtime/curators/updating-md5checksum-100020.txt" ]
}

@test "Invalid DOI" {
     run ./filesMetaToDb.sh 888888
     [[ "$output" =~ "No dataset found in database with DOI 888888" ]]
     [[ "$output" =~ "Exception 'Exception' with message 'https://s3.ap-northeast-1.amazonaws.com/gigadb-datasets-metadata/888888.filesizes not found'" ]]

     [ -f "../readme-generator/runtime/curators/updating-file-size-888888.txt" ]
     [ -f "../readme-generator/runtime/curators/updating-md5checksum-888888.txt" ]
}