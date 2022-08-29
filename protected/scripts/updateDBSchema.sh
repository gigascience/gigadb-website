#!/usr/bin/env bash

set -exu

./protected/yiic migrate --migrationPath=application.migrations.schema --interactive=0
./protected/yiic migrate --migrationPath=application.migrations.fix_import --interactive=0