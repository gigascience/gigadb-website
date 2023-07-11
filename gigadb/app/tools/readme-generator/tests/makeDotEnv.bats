#!/usr/bin/env bats

source ../../configurator/src/dotfiles.sh


teardown () {
    if [ -f tests/.env ];then
        rm tests/.env
    fi
    if [ -f tests/config-sources/env.example ];then
        rm tests/config-sources/env.example
        rmdir tests/config-sources
    fi
}

@test ".env already exists, just source it" {
    echo "foo=bar" > tests/.env
    run makeDotEnv tests
    [[ "$output" =~ "An .env file is present" ]] || false
}

@test "can source an env example" {
    mkdir -p tests/config-sources
    echo "foo=bar" > tests/config-sources/env.example
    run makeDotEnv tests
    [[ $output =~ "An .env file wasn't present, creating a new one from the default example" ]] || false
    [ -f tests/.env ]
}

@test "generate .env on the fly if no example and not one exists" {
    run makeDotEnv tests
    [[ $output =~ "Neither .env file or default example were present, generating one on the fly" ]] || false
    [ -f tests/.env ]
}

@test "can source .env in any case" {
        echo "foo=bar" > tests/bats/.env
        run makeDotEnv tests
        [[ "$output" =~ "Sourcing .env" ]] || false
}