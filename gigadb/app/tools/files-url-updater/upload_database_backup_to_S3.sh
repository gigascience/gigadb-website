#!/usr/bin/env bash

source ./.env

export PATH=/usr/local/bin/:$PATH
# preflight checks
which rclone

# Calculate dates
latest=$(date +"%Y%m%d")

gitlab_environment=$GIGADB_ENVIRONMENT
gitlab_project=$GITLAB_PROJECT_STRING

# Terminate other connections to RDS instance
export PGPASSWORD=$DB_PG_PASSWORD; psql -U $DB_PG_USER -d postgres -h $DB_PG_HOST -p 5432 -c  "SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname='gigadb';"

# Create pgdmp database dump file
export PGPASSWORD=$DB_PG_PASSWORD; pg_dump --host=$DB_PG_HOST -p 5432 --username=$DB_PG_USER --clean --create --schema=public --no-privileges --no-tablespaces --dbname=gigadb --file=backups/gigadb_${gitlab_project}_${gitlab_environment}_${latest}.backup

# Upload dump files in backups folder to S3 using rclone - skips identical files
rclone copy backups/ s3_remote:gigadb-database-backups
cloneStatus=$?

# Housekeeping
if [[ $cloneStatus -eq 0 ]];then
  rm -f backups/gigadb_${gitlab_project}_${gitlab_environment}_${latest}.backup
fi
