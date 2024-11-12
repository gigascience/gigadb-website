#!/usr/bin/env bats

# Readme file will be created in the current working directory from where this 
# bats script is called
WORKING_DIR=$(pwd)

teardown () {
    echo "executing teardown code"
    FILES="${WORKING_DIR}/readme_100142.txt
    ${WORKING_DIR}/readme_100006.txt
    ${WORKING_DIR}/readme_100020.txt"

    for file in $FILES
    do
      echo "Deleting $file"
      if [ -f "$file" ] ; then
          rm "$file"
      fi
    done
}

@test "Output usage information" {
    run ./createReadme.sh
    [ "$status" -eq 1 ]
    [[ "$output" =~ "Usage: ./createReadme.sh --doi <DOI>" ]]
    [[ "$output" =~ "Required:" ]]
    [[ "$output" =~ "--doi            DOI to process" ]]
    [[ "$output" =~ "Available Options:" ]]
    [[ "$output" =~ "--batch          Number of DOI to process" ]]
    [[ "$output" =~ "--wasabi         (Default) Copy readme file to Wasabi bucket" ]]
    [[ "$output" =~ "--apply          Escape dry run mode" ]]
    [[ "$output" =~ "--use-live-data  Copy data to production live bucket" ]]
}

@test "DOI is required" {
    run ./createReadme.sh --doi
    [ "$status" -eq 1 ]
    [[ "$output" =~ "Error: --doi <DOI> is required." ]]
}

@test "invalid option is provided" {
    run ./createReadme.sh 100142
    [ "$status" -eq 1 ]
    [[ "$output" =~ "Invalid option: 100142" ]]
}

@test "create readme file" {
    ./createReadme.sh --doi 100142
    # Check readme file has been created
    [ -f "${WORKING_DIR}"/readme_100142.txt ]
}

@test "check does not create readme with invalid doi and exits" {
    ./createReadme.sh --doi 100005
    # Check readme file does not exist
    [ ! -f "${WORKING_DIR}"/readme_100005.txt ]
    # Ensure script has exited by checking it does not go on to create readme file
    [ ! -f "${WORKING_DIR}"/readme_100006.txt ]
}

@test "create one readme file using batch mode" {
    ./createReadme.sh --doi 100005 --batch 1
    # Check readme file has been created
    [ -f "${WORKING_DIR}"/readme_100006.txt ]
    # Ensure script exits by checking it does not go on to create next readme file
    [ ! -f "${WORKING_DIR}"/readme_100020.txt ]
}

@test "create two readme files using batch mode" {
    ./createReadme.sh --doi 100005 --batch 2
    # Check two readme files has been created
    [ -f "${WORKING_DIR}"/readme_100006.txt ]
    [ -f "${WORKING_DIR}"/readme_100020.txt ]
}