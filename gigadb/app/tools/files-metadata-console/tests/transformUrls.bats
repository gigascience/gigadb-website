#!/usr/bin/env bats

setup () {
    out=$(docker-compose run -T files-metadata-console psql -X -U gigadb -h database -d gigadb -c 'SELECT ftp_site FROM dataset WHERE id = 8;')
    # echo "$out" >&3
    if [[ "$out" == *" https://s3.ap-northeast-1.wasabisys.com"* ]]; then
      echo "Dev database contains Wasabi links - execute ./up.sh to reset database and run this test again" >&2
      exit 1
    fi
}

teardown () {
    echo "executing teardown code"
}

@test "transform dataset and file URLs" {
  echo 'Running test' >&3
  scripts/updateFileUrls.sh
  out=$(docker-compose run -T files-metadata-console psql -X -U gigadb -h database -d gigadb -c 'SELECT ftp_site FROM dataset WHERE id = 8;')
  echo "$out" >&3
  if [[ "$out" == *" https://s3.ap-northeast-1.wasabisys.com"* ]]; then
    echo "Files in dev database contains Wasabi links" >&3
  fi
}
