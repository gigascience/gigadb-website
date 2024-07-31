#!/usr/bin/env bash

set -exu

source /home/centos/.env

# Calculate dates
latest=$(date --date="1 days ago" +"%Y%m%d")
if [ -z ${1+x} ];then
  backupDate=$latest
elif [ $1 == "latest" ];then
  backupDate=$latest
else
  backupDate=$1
fi

# Prepare constraints
dropconstraints="SELECT 'ALTER TABLE '||nspname||'.\"'||relname||'\" DROP CONSTRAINT \"'||conname||'\";'
FROM pg_constraint
INNER JOIN pg_class ON conrelid=pg_class.oid
INNER JOIN pg_namespace ON pg_namespace.oid=pg_class.relnamespace
WHERE nspname NOT IN ('pg_catalog', 'information_schema')
ORDER BY CASE WHEN contype='f' THEN 0 ELSE 1 END,contype,nspname,relname,conname;"
addconstraints="SELECT 'ALTER TABLE '||nspname||'.\"'||relname||'\" ADD CONSTRAINT \"'||conname||'\" '|| pg_get_constraintdef(pg_constraint.oid)||';'
FROM pg_constraint
INNER JOIN pg_class ON conrelid=pg_class.oid
INNER JOIN pg_namespace ON pg_namespace.oid=pg_class.relnamespace
WHERE nspname NOT IN ('pg_catalog', 'information_schema')
ORDER BY CASE WHEN contype='f' THEN 0 ELSE 1 END DESC,contype DESC,nspname DESC,relname DESC,conname DESC;"
PGPASSWORD=$PGPASSWORD psql -h $PGHOST -U $PGUSER -d $PGDATABASE -t -c "$dropconstraints" > /tmp/dropconstraints.sql
PGPASSWORD=$PGPASSWORD psql -h $PGHOST -U $PGUSER -d $PGDATABASE -t -c "$addconstraints" > /tmp/addconstraints.sql

# Download the upstream backup and load it into RDS
docker run --rm --env-file .env -v /home/centos/.config/rclone/rclone.conf:/root/.config/rclone/rclone.conf -v /home/centos/restore:/restore --entrypoint /restore_database_from_s3_backup.sh registry.gitlab.com/$GITLAB_PROJECT_NAME/production_s3backup:$GIGADB_ENVIRONMENT "$backupDate"

# Drop constraints/indexes/triggers before running migrations
PGPASSWORD=$PGPASSWORD psql -h $PGHOST -U $PGUSER -d $PGDATABASE < /tmp/dropconstraints.sql

# Run migrations
docker run --rm -e YII_PATH=/var/www/vendor/yiisoft/yii -v /home/centos:/var/www/protected/runtime registry.gitlab.com/$GITLAB_PROJECT_NAME/production_app:$GIGADB_ENVIRONMENT /var/www/protected/scripts/updateDBSchema.sh

# Restore constraints/indexes/triggers
PGPASSWORD=$PGPASSWORD psql -h $PGHOST -U $PGUSER -d $PGDATABASE < /tmp/addconstraints.sql

# Housekeeping
rm -f /home/centos/restore/gigadb*.backup
rm /tmp/*constraints.sql
