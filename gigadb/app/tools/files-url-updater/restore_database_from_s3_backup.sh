#!/usr/bin/env bash

source ./.env

export PATH=/usr/local/bin/:$PATH

# Calculate dates
latest=$(date --date="1 days ago" +"%Y%m%d")
backupDate="$1"

gitlab_environment=$GIGADB_ENVIRONMENT
gitlab_project=$GITLAB_PROJECT_STRING

echo "Downloading database dump from S3"
if [[ -z $backupDate || $backupDate -eq "latest" ]];then
  rclone copy s3_remote:gigadb-database-backups/gigadb_${gitlab_project}_${gitlab_environment}_${latest}.backup restore/
else
  rclone copy s3_remote:gigadb-database-backups/gigadb_${gitlab_project}_${gitlab_environment}_${backupDate}.backup restore/
fi

echo "Terminating other connections to RDS instance"
export PGPASSWORD=$DB_PG_PASSWORD; psql -U $DB_PG_USER -d postgres -h $DB_PG_HOST -p 5432 -c "SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname='gigadb';"
export PGPASSWORD=$DB_PG_PASSWORD; psql -U $DB_PG_USER -d postgres -h $DB_PG_HOST -p 5432 -c "drop database if exists $DB_PG_DATABASE"
export PGPASSWORD=$DB_PG_PASSWORD; psql -U $DB_PG_USER -d postgres -h $DB_PG_HOST -p 5432 -c "create database $DB_PG_DATABASE owner $DB_PG_USER"

echo "Restoring dump onto RDS"
if [[ -z $backupDate || $backupDate -eq "latest" ]];then
  echo "Loading gigadb_${latest}.backup"
  export PGPASSWORD=$DB_PG_PASSWORD; psql -U $DB_PG_USER -h $DB_PG_HOST -p 5432 < restore/gigadb_${gitlab_project}_${gitlab_environment}_${latest}.backup
else
  echo "Loading gigadb_${backupDate}.backup"
  export PGPASSWORD=$DB_PG_PASSWORD; psql -U $DB_PG_USER -h $DB_PG_HOST -p 5432 < restore/gigadb_${gitlab_project}_${gitlab_environment}_${backupDate}.backup
fi