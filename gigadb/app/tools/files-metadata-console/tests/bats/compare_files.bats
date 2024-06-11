@test "Show usage when no arguments passed" {
	run scripts/compare_files.sh
	[ "$output" = "Usage: scripts/compare_files.sh <file_list> <search_directory> [-v]" ]
  [ "$status" -eq 1 ]
}

@test "Non existing file list" {
	run scripts/compare_files.sh tests/_data/compare_files/gaigh tests/_data/compare_files/user0
	[ "$output" = "File list tests/_data/compare_files/gaigh not found!" ]
  [ "$status" -eq 1 ]
}

@test "Non existing search directory" {
	run scripts/compare_files.sh tests/_data/compare_files/user0files fasfh
	[ "$output" = "Search directory fasfh not found!" ]
  [ "$status" -eq 1 ]
}

@test "Empty file list" {
	run scripts/compare_files.sh tests/_data/compare_files/emptylist tests/_data/compare_files/user0
	[[ "$output" =~ "Files found from the list: 0" ]]
	[[ "$output" =~ "Files not found from the list: 0" ]]
  [ "$status" -eq 0 ]
}

@test "non empty and existing file list and search directory passed to the command" {
	run scripts/compare_files.sh tests/_data/compare_files/user0files tests/_data/compare_files/user0
	[[ "$output" =~ "Files found from the list: 6" ]]
	[[ "$output" =~ "Files not found from the list: 1" ]]
	[[ "$output" =~ "Files in tests/_data/compare_files/user0 not listed in tests/_data/compare_files/user0files: 2" ]]
  [ "$status" -eq 0 ]
}

@test "when the file list has blank lines in it" {
	run scripts/compare_files.sh tests/_data/compare_files/emptylines tests/_data/compare_files/user0
	[[ "$output" =~ "Files found from the list: 6" ]]
	[[ "$output" =~ "Files not found from the list: 1" ]]
	[[ "$output" =~ "Files in tests/_data/compare_files/user0 not listed in tests/_data/compare_files/emptylines: 2" ]]
  [ "$status" -eq 0 ]
}

@test "Dot files are picked up in both directions" {
	run scripts/compare_files.sh tests/_data/compare_files/emptylines tests/_data/compare_files/user0
	[[ "$output" =~ "a/b/.hidden-file not listed" ]]
	[[ "$output" =~ "No files found for .env" ]]
  [ "$status" -eq 0 ]
}

@test "Default non verbose mode only shows discrepancies" {
	run scripts/compare_files.sh tests/_data/compare_files/emptylines tests/_data/compare_files/user0
	[[ ! "$output" =~ "Checking whether files from" ]]
	[[ ! "$output" =~ "Files found for README_000000.txt" ]]
	[[ ! "$output" =~ "tests/_data/compare_files/user0/README_000000.txt" ]]
	[[ "$output" =~ "No files found for .env" ]]
}

@test "Verbose mode shows all files and whether a match was found or not" {
	run scripts/compare_files.sh tests/_data/compare_files/emptylines tests/_data/compare_files/user0 -v
	[[ "$output" =~ "Checking whether files from" ]]
	[[ "$output" =~ "Files found for README_000000.txt" ]]
	[[ "$output" =~ "tests/_data/compare_files/user0/README_000000.txt" ]]
	[[ "$output" =~ "No files found for .env" ]]
}
