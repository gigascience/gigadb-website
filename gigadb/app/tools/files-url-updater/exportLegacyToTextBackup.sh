#!/usr/bin/env bash

set -ex

destFile=$1
pg_dump --host=pg9_3 --port=5432  --username=gigadb  --clean --create --schema=public --no-privileges --no-tablespaces --dbname=gigadb --file=$destFile