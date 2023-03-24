#!/usr/sbin/env bats

source ./src/dotfiles.sh

teardown () {
	if [ -f tests/.secrets ];then
		rm tests/.secrets
	fi
}

@test "source .secrets if already exists" {
    echo "foo=bar" > tests/.secrets
    run makeDotSecrets tests
    [ "$output" = "Sourcing secrets" ]
}

# bats test_tags=TODO
@test "can generate .secret" {
    skip
}