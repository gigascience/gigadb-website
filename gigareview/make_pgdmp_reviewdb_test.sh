#!/usr/bin/env bash
set -e

source .env
source .secrets

export PGPASSWORD=$REVIEW_DB_PASSWORD
export testDB="${REVIEW_DB_DATABASE}_test"

pg_dump --no-owner -U $REVIEW_DB_USERNAME -h $REVIEW_DB_HOST -p $REVIEW_DB_PORT -F custom -d $testDB -f sql/"$testDB".pgdmp