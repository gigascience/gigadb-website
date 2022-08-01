#!/usr/bin/env bash

set -ex

./protected/yiic custommigrations dropconstraints
./protected/yiic custommigrations dropindexes
./protected/yiic custommigrations droptriggers
./protected/yiic migrate --migrationPath=application.migrations.schema --interactive=0
./protected/yiic migrate --migrationPath=application.migrations.fix_import --interactive=0