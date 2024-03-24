#!/usr/bin/env bats

teardown () {
    echo "Executing teardown code"
    rm ./102480.md5
    rm ./102480.filesizes
}

@test "create md5 and filesizes files for dataset" {
    cd ../_data/102480
    ls ../../../scripts
    ../../../scripts/md5.sh
    # Check files have been created
    [ -f ./102480.md5 ]
    [ -f ./102480.filesizes ]
    # Check contents in files created by md5.sh
    # Use echo -e "$output" >&3 to print grep output to your terminal
    run grep './analysis_data/Tree_file.txt' ./102480.md5
    [ "$output" = '67d9336ca3b61384185dc665026a2325  ./analysis_data/Tree_file.txt' ]
    run grep './readme_102480.txt' ./102480.filesizes
    [ "$output" = '    3202	./readme_102480.txt' ]
}
