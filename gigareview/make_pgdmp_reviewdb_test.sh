#!/usr/bin/env bash
set -e

source .env
source .secrets

export PGPASSWORD=$REVIEW_DB_PASSWORD

pg_dump --no-owner -U $REVIEW_DB_USERNAME -h $REVIEW_DB_HOST -p $REVIEW_DB_PORT -F custom -d $REVIEW_DB_DATABASE -f sql/"${REVIEW_DB_DATABASE}_test".pgdmp