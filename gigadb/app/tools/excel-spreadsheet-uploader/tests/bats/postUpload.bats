#!/usr/bin/env bats

teardown () {
    echo "executing teardown code"
    FILES="../readme-generator/runtime/curators/readme_100006.txt
    ../readme-generator/runtime/curators/updating-file-size-100006.txt
    ../readme-generator/runtime/curators/updating-md5checksum-100006.txt
    ../readme-generator/runtime/curators/readme_100020.txt
    ../readme-generator/runtime/curators/updating-file-size-100020.txt
    ../readme-generator/runtime/curators/updating-md5checksum-100020.txt
    ../readme-generator/runtime/curators/updating-file-size-888888.txt
    ../readme-generator/runtime/curators/updating-md5checksum-888888.txt
    ../readme-generator/uploadDir/readme_100006_"*".log
    ../readme-generator/uploadDir/readme_100020_"*".log
    ../readme-generator/uploadDir/readme_888888_"*".log"

    for file in $FILES
    do
      echo "Deleting $file"
      if [ -f "$file" ] ; then
          rm "$file"
      fi
    done

}

@test "No DOI provided" {
    run ./postUpload.sh
    [ "$status" -eq 1 ]
    [ "${lines[0]}" = "Usage: ./postUpload <DOI>" ]
}

@test "DOI with existing filesize file and md5 file in s3 bucket" {
    run ./postUpload.sh 100006
    [[ "$output" =~ "* About to create the README file for 100006" ]]
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

    [ -f "../readme-generator/runtime/curators/readme_100006.txt" ]
    [ -f "../readme-generator/runtime/curators/updating-file-size-100006.txt" ]
    [ -f "../readme-generator/runtime/curators/updating-md5checksum-100006.txt" ]

    # check rclone log
    [ -f "../readme-generator/uploadDir/readme_100006_"*".log" ]
    run grep "Successfully copied file to Wasabi for DOI: 100006" ../readme-generator/uploadDir/readme_100006_*.log
    [ "$status" -eq 0 ]
}

@test "DOI with non-existing md5 file in s3 bucket" {
    run ./postUpload.sh 100020
    [[ "$output" =~ "* About to create the README file for 100020" ]]
    [[ "$output" =~ "Done with creating the README file for 100020. The README file is saved in file: /home/curators/readme-100020.txt" ]]
    [[ "$output" =~ "* About to update files' size and MD5 checksum for 100020" ]]
    [[ "$output" =~ "No 100020.md5 file could be found for dataset DOI 100020" ]]
    [[ "$output" =~ "Done with updating files' size and MD5 checksum for 100020" ]]

    [ -f "../readme-generator/runtime/curators/readme_100020.txt" ]
    [ -f "../readme-generator/runtime/curators/updating-file-size-100020.txt" ]
    [ -f "../readme-generator/runtime/curators/updating-md5checksum-100020.txt" ]

    # check rclone log
    [ -f "../readme-generator/uploadDir/readme_100020_"*".log" ]
    run grep "Successfully copied file to Wasabi for DOI: 100020" ../readme-generator/uploadDir/readme_100020_*.log
    [ "$status" -eq 0 ]
}

@test "Invalid DOI" {
     run ./postUpload.sh 888888
     [[ "$output" =~ "Dataset 888888 not found" ]]
     [[ "$output" =~ "No dataset found in database with DOI 888888" ]]
     [[ "$output" =~ "Exception 'Exception' with message 'https://s3.ap-northeast-1.amazonaws.com/gigadb-datasets-metadata/888888.filesizes not found'" ]]

     [ -f "../readme-generator/runtime/curators/updating-file-size-888888.txt" ]
     [ -f "../readme-generator/runtime/curators/updating-md5checksum-888888.txt" ]

     # check rclone log
     [ -f "../readme-generator/uploadDir/readme_888888_"*".log" ]
     run grep "No dataset for DOI 888888" ../readme-generator/uploadDir/readme_888888_*.log
     [ "$status" -eq 0 ]
}