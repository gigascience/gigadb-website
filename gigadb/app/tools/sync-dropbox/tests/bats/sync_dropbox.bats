teardown () {
    echo "executing teardown code"
    FILES="logs/sync_dropbox_"*".log"

    for file in $FILES
    do
      echo "Deleting $file"
      if [ -f "$file" ] ; then
          rm "$file"
      fi
    done

}

@test "No parameter provided" {
    run scripts/sync_dropbox.sh
    [ "$status" -eq 1 ]
    [[ "$output" =~ "Usage: scripts/sync_dropbox.sh <option>" ]]
    [[ "$output" =~ "Available Option:" ]]
    [[ "$output" =~ "--dry-run        Do a trial run" ]]
    [[ "$output" =~ "--apply          Escape dry run mode" ]]
    [[ "$output" =~ "Example usages:" ]]
    [[ "$output" =~ "scripts/sync_dropbox.sh --dry-run" ]]
    [[ "$output" =~ "scripts/sync_dropbox.sh --apply" ]]
}

@test "Execute in dry run" {
    run scripts/sync_dropbox.sh --dry-run
    [ "$status" -eq 0 ]
    [[ "$output" =~ "INFO : Start sync dropbox from production to alt-production" ]]
    [[ "$output" =~ "INFO  : Executed: rclone sync production-staging:/share/dropbox/ /share/dropbox --dry-run --config config/rclone.conf" ]]
    [[ "$output" =~ "INFO  : Successfully sync dropbox from production to alt-production" ]]
}