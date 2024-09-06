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

    rm -rf data/share/dropbox/*
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

@test "Execute in dry run mode" {
    run scripts/sync_dropbox.sh --dry-run
    [ "$status" -eq 0 ]

    expected_lines=(
        "INFO : Start sync dropbox from production-staging to alt dev"
        "NOTICE: rija_test.txt: Skipped copy as --dry-run is set (size 166)"
        "NOTICE: user27: Skipped set directory modification time as --dry-run is set"
        "INFO  : Executed: rclone sync production-staging:/share/dropbox/ data/share/dropbox --dry-run --config config/rclone.conf"
        "INFO  : Successfully sync dropbox from production-staging to alt dev"
    )

    # Check the log
    for line in "${expected_lines[@]}"; do
        run grep -F "$line" logs/sync_dropbox_*.log
        [ "$status" -eq 0 ]
    done
}

@test "Execute in apply mode" {
    run scripts/sync_dropbox.sh --apply
    [ "$status" -eq 0 ]

    expected_lines=(
        "INFO : Start sync dropbox from production-staging to alt dev"
        "INFO  : user27/.dotfiles.txt: Copied (new)"
        "INFO  : rija_test.txt: Copied (new)"
        "INFO  : user0/change.log: Copied (new)"
        "INFO  : user4/brassicaceae_NCBI/amas.tar.gz: Copied (new)"
        "INFO  : user109: Set directory modification time (using DirSetModTime)"
        "INFO  : Executed: rclone sync production-staging:/share/dropbox/ data/share/dropbox --config config/rclone.conf"
        "INFO  : Successfully sync dropbox from production-staging to alt dev"
    )

    # Check the log
    for line in "${expected_lines[@]}"; do
        run grep -F "$line" logs/sync_dropbox_*.log
        [ "$status" -eq 0 ]
    done
}
