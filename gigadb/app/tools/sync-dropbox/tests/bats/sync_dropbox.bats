eardown () {
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

@test "No parameter provided {
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