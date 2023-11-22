# Update dataset ftp_site and file location URLs in database

# Stop script upon error
set -e
# Treat unset or null variables as an error and exit
set -u

PATH=/usr/local/bin:$PATH
export PATH

export WASABI_SERVICE_URL="s3.ap-northeast-1.wasabisys.com"
export DESTINATION_BUCKET_DIRECTORY="https://${WASABI_SERVICE_URL}/gigadb-datasets/live/pub"
export FTP_SITE_CNGB="https://ftp.cngb.org/pub/gigadb/pub"
export FTP_SITE_PARROT="ftp://parrot.genomics.cn/gigadb/pub"
export FTP_SITE_CLIMB="ftp://climb.genomics.cn/pub"

# Conditional to execute SQL commands on staging or live environment
if [[ $(uname -n) =~ compute ]];then
  source .env
  export PGPASSWORD=${PGPASSWORD}
  # Make use of psql installed on bastion server
  EXECUTE_SQL="psql -X -U ${PGUSER} -h ${PGHOST} -d ${PGDATABASE} --set ON_ERROR_STOP=on --set AUTOCOMMIT=off --set bucketdir=${DESTINATION_BUCKET_DIRECTORY} --set ftp_site_cngb=${FTP_SITE_CNGB} --set ftp_site_parrot=${FTP_SITE_PARROT} --set ftp_site_climb=${FTP_SITE_CLIMB}"
else
  # We are in dev environment
  source .secrets
  EXECUTE_SQL="docker-compose run -T files-metadata-console psql -X -U ${GIGADB_USER} -h ${GIGADB_HOST} -d ${GIGADB_DB} --set ON_ERROR_STOP=on --set AUTOCOMMIT=off --set bucketdir=${DESTINATION_BUCKET_DIRECTORY} --set ftp_site_cngb=${FTP_SITE_CNGB} --set ftp_site_parrot=${FTP_SITE_PARROT} --set ftp_site_climb=${FTP_SITE_CLIMB}"
fi

$EXECUTE_SQL <<SQL
\echo Processing dataset table...

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
\echo Created dataset_changes temporary table 

UPDATE
  dataset_changes
SET
  ftp_site = REPLACE(
    ftp_site, :'ftp_site_cngb',
    :'bucketdir'
  );

UPDATE
  dataset_changes
SET ftp_site = REPLACE(
    ftp_site, :'ftp_site_parrot',
    :'bucketdir'
    );

UPDATE
  dataset_changes
SET ftp_site = REPLACE(
    ftp_site, :'ftp_site_climb',
    :'bucketdir'
  );
\echo Transformed URLs in ftp_site column in temp table

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
\echo Copied URLs from temporary table into dataset table ftp_site column

SET vars.bucketdir to :'bucketdir';
DO \$$
DECLARE
  bucketdir_value TEXT := current_setting('vars.bucketdir');
  expected_row_changes INTEGER;
  actual_row_changes INTEGER;
BEGIN
  SELECT COUNT(*) INTO expected_row_changes FROM dataset_changes;
  SELECT COUNT(*) INTO actual_row_changes FROM dataset WHERE ftp_site LIKE '%' || bucketdir_value || '%';
  ASSERT actual_row_changes = expected_row_changes, 'No. of row changes in dataset table does not equal no. of rows in dataset_changes table!';
END
\$$;
\echo Asserted that all rows in temporary table were copied into dataset table

\echo Processing file table...

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
  location LIKE '%' || :'ftp_site_parrot' || '%'
OR
  location LIKE '%' || :'ftp_site_climb' || '%'
OR
  location LIKE '%' || :'ftp_site_cngb' || '%'
ORDER BY
  dataset_id ASC;
\echo Created dataset_changes temporary table 

UPDATE
  file_changes
SET location = REPLACE(
  location, :'ftp_site_cngb',
  :'bucketdir'
);

UPDATE
  file_changes
SET location = REPLACE(
  location, :'ftp_site_parrot',
  :'bucketdir'
);

UPDATE
  file_changes
SET location = REPLACE(
  location, :'ftp_site_climb',
  :'bucketdir'
);
\echo Transformed URLs in location column in temp table

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
\echo Copied URLs from temporary table into dataset table ftp_site column

SET vars.bucketdir to :'bucketdir';
DO \$$
DECLARE
  bucketdir_value TEXT := current_setting('vars.bucketdir');
  expected_row_changes INTEGER;
  actual_row_changes INTEGER;
BEGIN
  SELECT COUNT(*) INTO expected_row_changes FROM file_changes;
  SELECT COUNT(*) INTO actual_row_changes FROM file WHERE location LIKE '%' || bucketdir_value || '%';
  ASSERT actual_row_changes = expected_row_changes, 'No. of row changes in file table does not equal no. of rows in file_changes table!';
END
\$$;
\echo Asserted that all rows in temporary table were copied into file table

-- This transaction will only commit if all PSQL commands passes
COMMIT;
\echo All SQL commands were successfully committed in database transaction!
SQL