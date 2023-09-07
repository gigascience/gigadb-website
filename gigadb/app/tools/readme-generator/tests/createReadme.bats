#!/usr/bin/env bats

teardown () {
    echo "executing teardown code"
    rm runtime/curators/readme_100142.txt
}

@test "create readme file" {
    ./createReadme.sh --doi 100142 --outdir /home/curators
    # Check readme file has been created
    [ -f runtime/curators/readme_100142.txt ]
}