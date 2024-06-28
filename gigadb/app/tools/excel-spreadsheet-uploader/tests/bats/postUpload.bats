#!/usr/bin/env bats

teardown () {
    echo "executing teardown code"
    FILES="../readme-generator/runtime/curators/readme_100142.txt
    ../readme-generator/runtime/curators/updating-file-size-100142.txt
    ../readme-generator/runtime/curators/updating-md5checksum-100142.txt
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
    run ./postUpload.sh
    [ "$status" -eq 1 ]
    [ "$output" = "Usage: ./postUpload <DOI>" ]
}

@test "DOI provided" {
    run ./postUpload.sh 100142
    [[ "$output" =~ "* About to create the README file for 100142" ]]
    [[ "$output" =~ "Done with creating the README file for 100142. The README file is saved in file: /home/curators/readme-100142.txt" ]]
    [[ "$output" =~ "* About to update files' size and MD5 checksum for 100142" ]]
    [[ "$output" =~ "Done with updating files' size and MD5 checksum for 100142" ]]

    [ -f "../readme-generator/runtime/curators/readme_100142.txt" ]
    [ -f "../readme-generator/runtime/curators/updating-file-size-100142.txt" ]
    [ -f "../readme-generator/runtime/curators/updating-md5checksum-100142.txt" ]
}

@test "Invalid DOI" {
     run ./postUpload.sh 888888
     [[ "$output" =~ "Dataset 888888 not found" ]]
     [[ "$output" =~ "No dataset found in database with DOI 888888" ]]
     [[ "$output" =~ "Exception 'Exception' with message 'https://s3.ap-northeast-1.amazonaws.com/gigadb-datasets-metadata/888888.filesizes not found'" ]]

     [ -f "../readme-generator/runtime/curators/updating-file-size-888888.txt" ]
     [ -f "../readme-generator/runtime/curators/updating-md5checksum-888888.txt" ]
}