#!/usr/bin/env bats

teardown () {
    echo "executing teardown code"
    FILES="runtime/curators/readme_100142.txt
    runtime/curators/readme_100006.txt
    runtime/curators/readme_100020.txt
    uploadDir/readme_100005_"*".log
    uploadDir/readme_100142_"*".log"

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
    [[ "$output" =~ "--outdir         Specify the output directory" ]]
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
    ./createReadme.sh --doi 100142 --outdir /home/curators
    # Check readme file has been created
    [ -f runtime/curators/readme_100142.txt ]
}

@test "check does not create readme with invalid doi and exits" {
    ./createReadme.sh --doi 100005 --outdir /home/curators
    # Check readme file does not exist
    [ ! -f runtime/curators/readme_100005.txt ]
    # Ensure script has exited by checking it does not go on to create readme file
    [ ! -f runtime/curators/readme_100006.txt ]
}

@test "create one readme file using batch mode" {
    ./createReadme.sh --doi 100005 --outdir /home/curators --batch 1
    # Check readme file has been created
    [ -f runtime/curators/readme_100006.txt ]
    # Ensure script exits by checking it does not go on to create next readme file
    [ ! -f runtime/curators/readme_100020.txt ]
}

@test "create two readme files using batch mode" {
    ./createReadme.sh --doi 100005 --outdir /home/curators --batch 2
    # Check two readme files has been created
    [ -f runtime/curators/readme_100006.txt ]
    [ -f runtime/curators/readme_100020.txt ]
}