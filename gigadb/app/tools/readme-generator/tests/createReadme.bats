#!/usr/bin/env bats

#source ./createReadme.sh

teardown () {
    echo "executing teardown code"
    rm runtime/curators/readme_100142.txt
}

@test "create readme file" {
    ./createReadme.sh --doi 100142 --outdir /home/curators
}

@test "create readme file and copy to wasabi" {
    ./createReadme.sh --doi 100142 --outdir /home/curators --wasabi
}