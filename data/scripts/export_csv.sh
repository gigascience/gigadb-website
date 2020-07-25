#!/bin/bash
#
# Export tables from a GigaDB PostgreSQL database as CSV files

# bail out upon error
set -e
# bail out if an unset variable is used
set -u

######################################
# Set up command line argument parsing
######################################

usage="$(basename "$0") [-h] [-i o d] -- program to export table data from GigaDB PostgreSQL database as CSV files

where:
    -h  show this help text
    -i  set internal dataset identifiers
    -d  set name of database for exporting CSV data
    -o  set output directory to write CSV files"

DATASET_IDS_STR=""
# Name of database containing tables with production data to query
DATABASE_NAME=""

while [[ $# -gt 0 ]]; do
  opt="$1"

  case $opt in
    -d|--database)
      DATABASE_NAME="$2"
      echo "DATABASE_NAME: ${DATABASE_NAME}"
      shift # past argument
      shift # past value
      ;;
    -i|--ids)
      DATASET_IDS_STR="$2"
      echo "DATASET_IDS_STR: ${DATASET_IDS_STR}"
      shift # past argument
      shift # past value
      ;;
    -h|--help)
      echo "$usage"
      exit
      ;;
    -o|--output-dir)
      OUTPUT_DIR="$2"
      echo "OUTPUT_DIR: "$OUTPUT_DIR
      shift # past argument
      shift # past value
      ;;
    --default)
      DEFAULT=YES
      shift # past argument
      ;;
  esac
done

#########################
# Set up and sanity check
#########################

if [[ -z "${DATASET_IDS_STR}" ]]; then
  echo "No dataset identifiers have been set"
  exit
fi

if [[ -z "${DATABASE_NAME}" ]]; then
  echo "No database has been set"
  exit
fi

if [[ -z "${OUTPUT_DIR}" ]]; then
  echo "No output directory has been set"
  exit
fi

# Convert $DATASET_IDS_STR into DATASET_IDS array
DATASET_IDS=($DATASET_IDS_STR)

# Set working directory to this script directory
wd=$(cd -P -- "$(dirname -- "$0")" && pwd -P)

# Data directory
data_dir="${wd%/*}"

# Base project directory
base_dir="${data_dir%/*}"

# Output directory
output_dir_path="${wd%/*}/${OUTPUT_DIR}"
# Create OUTPUT_DIR if it doesn't exist
if [[ ! -d "${output_dir_path}" ]]; then
  echo "Creating directory: ${output_dir_path}"
  mkdir -p "${output_dir_path}"
else
  echo "${output_dir_path} directory already exists"
fi

# Check directories
echo "Data dir: ${data_dir}"
echo "Base dir: ${base_dir}"
echo "Working dir: ${wd}"
echo "Output dir: ${output_dir_path}"
echo "Database name: ${DATABASE_NAME}"

#######################################
# Returns id query string
# Globals:
#   DATASET_IDS
# Arguments:
#   An array of numbers, e.g, [4, 12]
# Outputs:
#   A string, e.g. "id = 4 or id = 12"
#######################################
function get_id_query_string() {
  id_query=""
  local pos
  pos=$((${#DATASET_IDS[*]} - 1))
  local last
  last=${DATASET_IDS[$pos]}

  for ID in "${DATASET_IDS[@]}"; do
    local current_id
    current_id="id = $ID"

    if [[ $ID == "$last" ]]; then
      id_query=$id_query" ${current_id}"
      echo "$id_query"
    else
      id_query=$id_query" ${current_id} or"
    fi
  done
}

#######################################
# Returns dataset_id query string
# Globals:
#   DATASET_IDS
# Arguments:
#   An array of numbers, e.g, [4, 12]
# Outputs:
#   A string, e.g. "dataset_id = 4 or dataset_id = 12"
#######################################
function get_dataset_id_query_string() {
  dataset_id_query=""
  local pos
  pos=$((${#DATASET_IDS[*]} - 1))
  local last
  last=${DATASET_IDS[$pos]}

  for ID in "${DATASET_IDS[@]}"; do
    current_dataset_id="dataset_id = $ID"

    if [[ $ID == "$last" ]]; then
      dataset_id_query=$dataset_id_query" ${current_dataset_id}"
      echo "${dataset_id_query}"
    else
      dataset_id_query=$dataset_id_query" ${current_dataset_id} or"
    fi
  done
}

##############
# Main program
##############

out_ids=$(get_id_query_string)
echo "SQL sub-string for SELECT query: ${out_ids}"

out_dataset_ids=$(get_dataset_id_query_string)
echo "SQL sub-string for SELECT query: ${out_dataset_ids}"

echo "Creating: dataset.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM dataset WHERE $out_ids ORDER BY id ASC) To '${output_dir_path}/dataset.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: publisher.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM publisher WHERE id IN (SELECT publisher_id FROM dataset WHERE $out_ids) ORDER BY id ASC) To '${output_dir_path}/publisher.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: manuscript.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM manuscript WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/manuscript.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: dataset_type.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM dataset_type WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/dataset_type.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: type.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM "type" WHERE id IN (SELECT type_id FROM dataset_type WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/type.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: external_link.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM external_link WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/external_link.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: external_link_type.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM "external_link_type" WHERE id IN (SELECT external_link_type_id FROM external_link WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/external_link_type.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: dataset_funder.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM dataset_funder WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/dataset_funder.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: funder_name.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM funder_name WHERE id IN (SELECT funder_id FROM dataset_funder WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/funder_name.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: link.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM link WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/link.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: dataset_log.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM dataset_log WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/dataset_log.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: curation_log.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM curation_log WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/curation_log.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: dataset_project.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM dataset_project WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/dataset_project.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: project.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM project WHERE id IN (SELECT project_id FROM dataset_project WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/project.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: image.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM image WHERE id IN (SELECT image_id FROM dataset WHERE $out_ids) ORDER BY id ASC) To '${output_dir_path}/image.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: dataset_attributes.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM dataset_attributes WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/dataset_attributes.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: relation.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM relation WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/relation.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: relationship.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM relationship WHERE id IN (SELECT relationship_id FROM relation WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/relationship.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: sample_rel.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM sample_rel WHERE relationship_id IN (SELECT relationship_id FROM relation WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/sample_rel.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: dataset_author.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM dataset_author WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/dataset_author.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: gigadb_user.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT DISTINCT * FROM gigadb_user WHERE id IN (SELECT submitter_id FROM dataset WHERE $out_ids) OR id IN (SELECT curator_id FROM dataset WHERE $out_ids) OR id IN (SELECT submitted_id FROM sample where id IN (SELECT sample_id FROM dataset_sample WHERE $out_dataset_ids)) ORDER BY id ASC) To '${output_dir_path}/gigadb_user.csv' With (FORMAT CSV, HEADER)
EOF
echo "Replacing user email addresses with test gigasciencejournal.com account"
count=0
while IFS=, read -r id email password; do
  if [[ "$count" -ne 0 ]]  # Skip header line in CSV file
  then
    new_email=test+"$id"@gigasciencejournal.com
    sed -i '' "s/${email}/${new_email}/g" "${output_dir_path}/gigadb_user.csv"
  fi
  (( count=count+1 ))
done < ${output_dir_path}/gigadb_user.csv
echo "Append 2 rows to provide test user@gigadb.org and admin@gigadb.org accounts"
echo ",admin@gigadb.org,5a4f75053077a32e681f81daa8792f95,Joe,Bloggs,BGI,admin,true,false,true,,,,,admin@gigadb.org,,EBI" >> ${output_dir_path}/gigadb_user.csv
echo ",user@gigadb.org,5a4f75053077a32e681f81daa8792f95,John,Smith,BGI,user,true,false,true,,,,,user@gigadb.org,,EBI" >> ${output_dir_path}/gigadb_user.csv

echo "Creating: author.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM author WHERE id IN (SELECT author_id FROM dataset_author WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/author.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: search.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM "search" WHERE user_id IN (SELECT submitter_id FROM dataset WHERE $out_ids) or user_id IN (SELECT curator_id FROM dataset WHERE $out_ids) ORDER BY id ASC) To '${output_dir_path}/search.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: dataset_sample.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM dataset_sample WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/dataset_sample.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: sample.csv"
# contact_author_name and contact_author_email not exported to ensure data privacy
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT id, species_id, name, consent_document, submitted_id, submission_date, sampling_protocol FROM sample WHERE id IN (SELECT sample_id FROM dataset_sample WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/sample.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: species.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM species WHERE id IN (SELECT species_id FROM sample WHERE id IN (SELECT sample_id FROM dataset_sample WHERE $out_dataset_ids)) ORDER BY id ASC) To '${output_dir_path}/species.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: alternative_identifiers.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM alternative_identifiers WHERE sample_id IN (SELECT sample_id FROM dataset_sample WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/alternative_identifiers.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: extdb.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM extdb WHERE id IN (SELECT extdb_id FROM alternative_identifiers WHERE sample_id IN (SELECT sample_id FROM dataset_sample WHERE $out_dataset_ids)) ORDER BY id ASC) To '${output_dir_path}/extdb.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: experiment.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM experiment WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/experiment.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: sample_experiment.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM sample_experiment WHERE experiment_id IN (SELECT id FROM experiment WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/sample_experiment.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: sample_attribute.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM sample_attribute WHERE sample_id IN (SELECT sample_id FROM dataset_sample WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/sample_attribute.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: attribute.csv"
# Attribute id 497 is urltoredirect attribute which is essential for dataset page display
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM attribute WHERE id IN (497) OR id IN (SELECT DISTINCT attribute_id FROM dataset_attributes WHERE $out_dataset_ids) OR id IN (SELECT DISTINCT attribute_id FROM exp_attributes WHERE exp_id IN (SELECT DISTINCT id FROM experiment WHERE $out_dataset_ids)) OR id IN (SELECT DISTINCT attribute_id FROM file_attributes WHERE file_id IN (SELECT DISTINCT id FROM file WHERE $out_dataset_ids)) OR id IN (SELECT DISTINCT attribute_id FROM sample_attribute WHERE sample_id IN (SELECT DISTINCT sample_id FROM dataset_sample WHERE $out_dataset_ids)) ORDER BY id ASC) To '${output_dir_path}/attribute.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: file.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM file WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/file.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: file_format.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM file_format WHERE id IN (SELECT format_id FROM file WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/file_format.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: file_type.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM file_type WHERE id IN (SELECT type_id FROM file WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/file_type.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: file_attributes.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM file_attributes WHERE file_id IN (SELECT id FROM file WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/file_attributes.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: file_relationship.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM file_relationship WHERE file_id IN (SELECT id FROM file WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/file_relationship.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: file_sample.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM file_sample WHERE file_id IN (SELECT id FROM file WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/file_sample.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: file_experiment.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM file_experiment WHERE file_id IN (SELECT id FROM file WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/file_experiment.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: exp_attributes.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM exp_attributes WHERE exp_id IN (SELECT id FROM experiment WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/exp_attributes.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: unit.csv"
# Export all rows from unit table
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM unit ORDER BY id ASC) To '${output_dir_path}/unit.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: news.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM news ORDER BY id ASC) To '${output_dir_path}/news.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: prefix.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM prefix ORDER BY id ASC) To '${output_dir_path}/prefix.csv' With (FORMAT CSV, HEADER)
EOF

echo "Creating: rss_message.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM rss_message ORDER BY id ASC) To '${output_dir_path}/rss_message.csv' With (FORMAT CSV, HEADER)
EOF
