# Update dataset ftp_site and file location URLs in database

# Stop script upon error
set -e
# Treat unset or null variables as an error and exit
set -u

PATH=/usr/local/bin:$PATH
export PATH

# Allow all scripts to base themselves from directory where backup script 
# is located
APP_SOURCE=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )

# Conditional to execute SQL commands on staging or live environment
if [[ $(uname -n) =~ compute ]];then
  source .env
  export PGPASSWORD=${PGPASSWORD}
  # Make use of psql installed on bastion server
  EXECUTE_SQL="psql -X -U ${PGUSER} -h ${PGHOST} -d ${PGDATABASE} --set ON_ERROR_STOP=on --set AUTOCOMMIT=off"
else
  # We are in dev environment
  source .secrets
  EXECUTE_SQL="docker-compose run -T files-metadata-console psql -X -U ${GIGADB_USER} -h ${GIGADB_HOST} -d ${GIGADB_DB} --set ON_ERROR_STOP=on --set AUTOCOMMIT=off"
fi

#######################################
# Set up logging
# Globals:
#   LOGDIR
#   APP_SOURCE
# Arguments:
#   None
#######################################
function set_up_logging() {
  LOGDIR="$APP_SOURCE/logs"
  LOGFILE="$LOGDIR/update_urls_$(date +'%Y%m%d_%H%M%S').log"
  mkdir -p "${LOGDIR}"
  touch "${LOGFILE}"
}

$EXECUTE_SQL <<SQL
-- #### Process dataset table ####

-- Temporary table will contain datasets that have 'ftp' in its ftp_site link
CREATE TEMPORARY TABLE dataset_changes AS
SELECT
  id,
  identifier,
  ftp_site
FROM
  dataset
WHERE
  ftp_site LIKE '%ftp%'
ORDER BY
  identifier ASC;

-- Transform URLs in ftp_site column
UPDATE
  dataset_changes
SET
  ftp_site = REPLACE(
    ftp_site, 'https://ftp.cngb.org/pub/gigadb/pub',
    'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub'
  );

UPDATE
  dataset_changes
SET ftp_site = REPLACE(
      ftp_site, 'ftp://parrot.genomics.cn/gigadb/pub', 
      'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub'
    );

UPDATE
  dataset_changes
SET ftp_site = REPLACE(
    ftp_site, 'ftp://climb.genomics.cn/pub', 
    'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub'
  );

-- Copy URLs from temporary table into dataset table's ftp_site column
UPDATE 
  dataset 
SET 
  ftp_site = subquery.ftp_site 
FROM 
  (
    SELECT 
      identifier, 
      ftp_site 
    FROM 
      dataset_changes
  ) AS subquery 
WHERE 
  dataset.identifier = subquery.identifier;

-- Assert that all rows in temporary table were copied into dataset table
DO \$$
DECLARE  
  expected_row_changes integer;
  actual_row_changes integer;
BEGIN
  SELECT COUNT(*) INTO expected_row_changes FROM dataset_changes;
  SELECT COUNT(*) INTO actual_row_changes FROM dataset WHERE ftp_site LIKE '%https://s3.ap-northeast-1.wasabisys.com%';
  ASSERT actual_row_changes = expected_row_changes, 'No. of row changes in dataset table does not equal no. of rows in dataset_changes table!';
END
\$$;

-- #### Process file table ####

-- Temporary table will contain files that have 'parrot.genomics.cn',
-- 'climb.genomics.cn' and 'ftp.cngb.org 'in its location link
CREATE TEMPORARY TABLE file_changes AS
SELECT
  id,
  dataset_id,
  location
FROM
  file
WHERE
  location LIKE '%parrot.genomics.cn%'
OR
  location LIKE '%climb.genomics.cn%'
OR
  location LIKE '%ftp.cngb.org%'
ORDER BY
  dataset_id ASC;

-- Transform URLs in location column
UPDATE
  file_changes
SET location = REPLACE(
  location, 'https://ftp.cngb.org/pub/gigadb/pub/',
  'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/'
);

UPDATE
  file_changes
SET location = REPLACE(
  location, 'ftp://parrot.genomics.cn/gigadb/pub/',
  'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/'
);

UPDATE
  file_changes
SET location = REPLACE(
  location, 'ftp://climb.genomics.cn/pub/',
  'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/'
);

-- Copy URLs from temporary table into dataset table's ftp_site column
UPDATE
  file
SET
  location=subquery.location
FROM
  (
    SELECT
      id,
      location
    FROM
      file_changes
  ) AS subquery
WHERE
  file.id=subquery.id;

-- Assert that all rows in temporary table were copied into file table
DO \$$
DECLARE  
  expected_row_changes integer;
  actual_row_changes integer;
BEGIN
  SELECT COUNT(*) INTO expected_row_changes FROM file_changes;
  SELECT COUNT(*) INTO actual_row_changes FROM file WHERE location LIKE '%https://s3.ap-northeast-1.wasabisys.com%';
  ASSERT actual_row_changes = expected_row_changes, 'No. of row changes in file table does not equal no. of rows in file_changes table!';
END
\$$;

-- This transaction will only commit if previous checks passes
COMMIT;
SQL