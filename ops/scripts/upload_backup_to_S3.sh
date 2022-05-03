#!/usr/bin/env bash

source /home/centos/files-url-updater/.env

export PATH=/usr/local/bin/:$PATH
# preflight checks
which rclone

# Calculate dates
latest=$(date --date="1 days ago" +"%Y%m%d")

# Terminate other connections to RDS instance
export PGPASSWORD=$DB_PG_PASSWORD; psql -U $DB_PG_USER -d postgres -h $DB_PG_HOST -p 5432 -c  "SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname='gigadb';"

# Create pgdmp database dump file
export PGPASSWORD=$DB_PG_PASSWORD; pg_dump --host=$DB_PG_HOST -p 5432 --username=$DB_PG_USER --clean --create --schema=public --no-privileges --no-tablespaces --dbname=gigadb --file=backups/gigadb_${latest}.backup

# Upload dump file to S3 using rclone
rclone copy ./backups/gigadb_${latest}.backup s3_remote:gigadb-database-backups
