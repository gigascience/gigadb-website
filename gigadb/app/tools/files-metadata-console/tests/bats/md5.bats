#!/usr/bin/env bats

teardown () {
    echo "Executing teardown code"
    FILES="tests/_data/dropbox/user4/102480.md5
    tests/_data/dropbox/user4/102480.filesizes"

    for file in $FILES
    do
      echo "Deleting $file"
      if [ -f "$file" ] ; then
          rm "$file"
      fi
    done
}

@test "Execute md5.sh within container" {
    docker-compose run --rm -w /gigadb/app/tools/files-metadata-console/tests/_data/dropbox/user4 files-metadata-console ../../../../scripts/md5.sh 102480
    [ -f tests/_data/dropbox/user4/102480.md5 ]
    run grep './analysis_data/Tree_file.txt' tests/_data/dropbox/user4/102480.md5
    [ "$output" = '67d9336ca3b61384185dc665026a2325  ./analysis_data/Tree_file.txt' ]

    [ -f tests/_data/dropbox/user4/102480.filesizes ]
    run grep './analysis_data/Tree_file.txt' tests/_data/dropbox/user4/102480.filesizes
    [ "$output" = '359	./analysis_data/Tree_file.txt' ]
}