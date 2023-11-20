#!/usr/bin/env bats

setup () {
    out=$(docker-compose run -T files-metadata-console psql -X -U gigadb -h database -d gigadb -c 'SELECT ftp_site FROM dataset WHERE id = 8;')
    # Check if above select query returns a Wasabi link
    if [[ "$out" == *" https://s3.ap-northeast-1.wasabisys.com"* ]]; then
      echo "ftp_site URL for dataset id 8 already has a Wasabi link - execute ./up.sh to reset database and run this test again" >&2
      exit 1
    fi
}

# Tests if the link in ftp_site column for dataset id 8 has been updated into a
# Wasabi link
@test "transform dataset and file URLs" {
  echo 'Running test' >&3
  scripts/updateFileUrls.sh
  out=$(docker-compose run -T files-metadata-console psql -X -U gigadb -h database -d gigadb -c 'SELECT ftp_site FROM dataset WHERE id = 8;')
  if [[ "$out" != *" https://s3.ap-northeast-1.wasabisys.com"* ]]; then
    echo "ftp_site URL for dataset id 8 was not transformed into Wasabi link" >&3
  fi
}
