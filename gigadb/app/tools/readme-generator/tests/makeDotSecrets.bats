#!/usr/bin/env bats

source ../../configurator/src/dotfiles.sh

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

@test "can generate .secrets" {
    [ ! -f tests/.secrets ]
    run makeDotSecrets tests
    [ -s tests/.secrets ]
}