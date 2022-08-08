#!/usr/bin/env bash

set -ex

destFile=${1:-"/converted/gigadbv3.backup"}
pg_dump --username=$POSTGRES_USER  --clean --create --schema=public --no-privileges --no-tablespaces --dbname=gigadb --file=$destFile