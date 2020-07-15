#!/bin/bash
#
# Export tables from a GigaDB PostgreSQL database as CSV files

################
# Set up logging
################

colblk='\033[0;30m' # Black - Regular
colred='\033[0;31m' # Red
colgrn='\033[0;32m' # Green
colylw='\033[0;33m' # Yellow
colpur='\033[0;35m' # Purple
colrst='\033[0m'    # Text Reset

verbosity=4

### Verbosity levels
silent_lvl=0
crt_lvl=1
err_lvl=2
wrn_lvl=3
ntf_lvl=4
inf_lvl=5
dbg_lvl=6

## esilent prints output even in silent mode
function esilent() { verb_lvl=$silent_lvl elog "$@"; }
function enotify() { verb_lvl=$ntf_lvl elog "$@"; }
function eok() { verb_lvl=$ntf_lvl elog "SUCCESS - $@"; }
function ewarn() { verb_lvl=$wrn_lvl elog "${colylw}WARNING${colrst} - $@"; }
function einfo() { verb_lvl=$inf_lvl elog "${colwht}INFO${colrst} ---- $@"; }
function edebug() { verb_lvl=$dbg_lvl elog "${colgrn}DEBUG${colrst} --- $@"; }
function eerror() { verb_lvl=$err_lvl elog "${colred}ERROR${colrst} --- $@"; }
function ecrit() { verb_lvl=$crt_lvl elog "${colpur}FATAL${colrst} --- $@"; }
function edumpvar() { for var in $@; do edebug "$var=${!var}"; done; }
function elog() {
  if [[ $verbosity -ge $verb_lvl ]]; then
    datestring=$(date +"%Y-%m-%d %H:%M:%S")
    echo -e "$datestring - $@"
  fi
}

######################################
# Set up command line argument parsing
######################################

usage="$(basename "$0") [-h] [-v i o d] -- program to export table data from GigaDB PostgreSQL database as CSV files

where:
    -h  show this help text
    -v  set logging verbosity (default: 4)
    -i  set internal dataset identifiers
    -d  set name of database for exporting CSV data
    -o  set output directory to write CSV files"

DATASET_IDS_STR=""
# Name of database containing tables with production data to query
DATABASE_NAME=""

POSITIONAL=()
while [[ $# -gt 0 ]]; do
  opt="$1"

  case $opt in
    -v|--verbosity)
      VERBOSITY="$2"
      if [[ "$VERBOSITY" -eq 0 ]]; then
        verbosity=$silent_lvl
        einfo "-v specified: Silent mode"
      elif [[ "$VERBOSITY" -eq 1 ]]; then
        verbosity=$crt_lvl
        einfo "-v specified: Critical mode"
      elif [[ "$VERBOSITY" -eq 2 ]]; then
        verbosity=$err_lvl
        einfo "-v specified: Error mode"
      elif [[ "$VERBOSITY" -eq 3 ]]; then
        verbosity=$wrn_lvl
        einfo "-v specified: Warn mode"
      elif [[ "$2" -eq "5" ]]; then
        verbosity=$inf_lvl
        einfo "-v specified: Info mode"
      elif [[ "$VERBOSITY" -eq 6 ]]; then
        verbosity=$dbg_lvl
        einfo "-v specified: Debug mode"
      fi
      shift # past argument
      shift # past value
      ;;
    -d|--database)
      DATABASE_NAME="$2"
      einfo "DATABASE_NAME: ${DATABASE_NAME}"
      shift # past argument
      shift # past value
      ;;
    -i|--ids)
      DATASET_IDS_STR="$2"
      einfo "DATASET_IDS_STR: ${DATASET_IDS_STR}"
      shift # past argument
      shift # past value
      ;;
    -h|--help)
      echo "$usage"
      exit
      ;;
    -o|--output-dir)
      OUTPUT_DIR="$2"
      einfo "OUTPUT_DIR: "$OUTPUT_DIR
      shift # past argument
      shift # past value
      ;;
    --default)
      DEFAULT=YES
      shift # past argument
      ;;
    *)    # unknown option
      POSITIONAL+=("$1") # save it in an array for later
      shift # past argument
      ;;
  esac
done
set -- "${POSITIONAL[@]}" # restore positional parameters

#########################
# Set up and sanity check
#########################

if [[ -z "${DATASET_IDS_STR}" ]]; then
  eerror "No dataset identifiers have been set"
  exit
fi

if [[ -z "${DATABASE_NAME}" ]]; then
  eerror "No database has been set"
  exit
fi

if [[ -z "${OUTPUT_DIR}" ]]; then
  eerror "No output directory has been set"
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
  einfo "Creating directory: ${output_dir_path}"
  mkdir -p "${output_dir_path}"
else
  edebug "${output_dir_path} directory already exists"
fi

# Check directories
einfo "Data dir: ${data_dir}"
einfo "Base dir: ${base_dir}"
einfo "Working dir: ${wd}"
einfo "Output dir: ${output_dir_path}"
einfo "Database name: ${DATABASE_NAME}"

#######################################
# Returns id query string, e.g. given
# an array [4, 12] then  string "id = 4
# or id = 12" will be returned.
# Globals:
#   DATASET_IDS
# Arguments:
#   None
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
# Returns dataset id query string, e.g.
# given an array [4, 12] then a string
# "dataset_id = 4 or dataset_id = 12"
# will be returned.
# Globals:
#   DATASET_IDS
# Arguments:
#   None
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
edebug "SQL sub-string for SELECT query: ${out_ids}"

out_dataset_ids=$(get_dataset_id_query_string)
edebug "SQL sub-string for SELECT query: ${out_dataset_ids}"

einfo "Creating: dataset.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM dataset WHERE $out_ids ORDER BY id ASC) To '${output_dir_path}/dataset.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: publisher.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM publisher WHERE id IN (SELECT publisher_id FROM dataset WHERE $out_ids) ORDER BY id ASC) To '${output_dir_path}/publisher.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: manuscript.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM manuscript WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/manuscript.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: dataset_type.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM dataset_type WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/dataset_type.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: type.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM "type" WHERE id IN (SELECT type_id FROM dataset_type WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/type.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: external_link.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM external_link WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/external_link.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: external_link_type.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM "external_link_type" WHERE id IN (SELECT external_link_type_id FROM external_link WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/external_link_type.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: dataset_funder.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM dataset_funder WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/dataset_funder.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: funder_name.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM funder_name WHERE id IN (SELECT funder_id FROM dataset_funder WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/funder_name.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: link.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM link WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/link.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: dataset_log.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM dataset_log WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/dataset_log.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: curation_log.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM curation_log WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/curation_log.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: dataset_project.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM dataset_project WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/dataset_project.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: project.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM project WHERE id IN (SELECT project_id FROM dataset_project WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/project.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: image.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM image WHERE id IN (SELECT image_id FROM dataset WHERE $out_ids) ORDER BY id ASC) To '${output_dir_path}/image.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: dataset_attributes.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM dataset_attributes WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/dataset_attributes.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: relation.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM relation WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/relation.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: relationship.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM relationship WHERE id IN (SELECT relationship_id FROM relation WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/relationship.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: sample_rel.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM sample_rel WHERE relationship_id IN (SELECT relationship_id FROM relation WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/sample_rel.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: dataset_author.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM dataset_author WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/dataset_author.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: gigadb_user.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM gigadb_user WHERE id IN (SELECT submitter_id FROM dataset WHERE $out_ids) or id IN (SELECT curator_id FROM dataset WHERE $out_ids) ORDER BY id ASC) To '${output_dir_path}/gigadb_user.csv' With (FORMAT CSV, HEADER)
EOF
einfo "Replacing user email addresses with test gigasciencejournal.com account"
count=0
while IFS=, read -r id email password; do
  if [[ "$count" -ne 0 ]]  # Skip header line in CSV file
  then
    new_email=test+"$id"@gigasciencejournal.com
    sed -i '' "s/${email}/${new_email}/g" "${output_dir_path}/gigadb_user.csv"
  fi
  (( count=count+1 ))
done < ${output_dir_path}/gigadb_user.csv
einfo "Append 2 rows to provide test user@gigadb.org and admin@gigadb.org accounts"
echo ",admin@gigadb.org,5a4f75053077a32e681f81daa8792f95,Joe,Bloggs,BGI,admin,true,false,true,,,,,admin@gigadb.org,,EBI" >> ${output_dir_path}/gigadb_user.csv
echo ",user@gigadb.org,5a4f75053077a32e681f81daa8792f95,John,Smith,BGI,user,true,false,true,,,,,user@gigadb.org,,EBI" >> ${output_dir_path}/gigadb_user.csv

einfo "Creating: author.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM author WHERE id IN (SELECT author_id FROM dataset_author WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/author.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: search.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM "search" WHERE user_id IN (SELECT submitter_id FROM dataset WHERE $out_ids) or user_id IN (SELECT curator_id FROM dataset WHERE $out_ids) ORDER BY id ASC) To '${output_dir_path}/search.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: dataset_sample.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM dataset_sample WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/dataset_sample.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: sample.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM sample WHERE id IN (SELECT sample_id FROM dataset_sample WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/sample.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: species.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM species WHERE id IN (SELECT species_id FROM sample WHERE id IN (SELECT sample_id FROM dataset_sample WHERE $out_dataset_ids)) ORDER BY id ASC) To '${output_dir_path}/species.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: alternative_identifiers.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM alternative_identifiers WHERE sample_id IN (SELECT sample_id FROM dataset_sample WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/alternative_identifiers.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: extdb.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM extdb WHERE id IN (SELECT extdb_id FROM alternative_identifiers WHERE sample_id IN (SELECT sample_id FROM dataset_sample WHERE $out_dataset_ids)) ORDER BY id ASC) To '${output_dir_path}/extdb.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: experiment.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM experiment WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/experiment.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: sample_experiment.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM sample_experiment WHERE experiment_id IN (SELECT id FROM experiment WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/sample_experiment.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: sample_attribute.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM sample_attribute WHERE sample_id IN (SELECT sample_id FROM dataset_sample WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/sample_attribute.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: attribute.csv"
# Attribute id 497 is urltoredirect attribute which is essential for dataset page display
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM attribute WHERE id IN (497) OR id IN (SELECT DISTINCT attribute_id FROM dataset_attributes WHERE $out_dataset_ids) OR id IN (SELECT DISTINCT attribute_id FROM exp_attributes WHERE exp_id IN (SELECT DISTINCT id FROM experiment WHERE $out_dataset_ids)) OR id IN (SELECT DISTINCT attribute_id FROM file_attributes WHERE file_id IN (SELECT DISTINCT id FROM file WHERE $out_dataset_ids)) OR id IN (SELECT DISTINCT attribute_id FROM sample_attribute WHERE sample_id IN (SELECT DISTINCT sample_id FROM dataset_sample WHERE $out_dataset_ids)) ORDER BY id ASC) To '${output_dir_path}/attribute.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: file.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM file WHERE $out_dataset_ids ORDER BY id ASC) To '${output_dir_path}/file.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: file_format.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM file_format WHERE id IN (SELECT format_id FROM file WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/file_format.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: file_type.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM file_type WHERE id IN (SELECT type_id FROM file WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/file_type.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: file_attributes.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM file_attributes WHERE file_id IN (SELECT id FROM file WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/file_attributes.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: file_relationship.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM file_relationship WHERE file_id IN (SELECT id FROM file WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/file_relationship.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: file_sample.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM file_sample WHERE file_id IN (SELECT id FROM file WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/file_sample.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: file_experiment.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM file_experiment WHERE file_id IN (SELECT id FROM file WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/file_experiment.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: exp_attributes.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM exp_attributes WHERE exp_id IN (SELECT id FROM experiment WHERE $out_dataset_ids) ORDER BY id ASC) To '${output_dir_path}/exp_attributes.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: unit.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM unit WHERE id IN (SELECT units_id FROM exp_attributes WHERE exp_id IN (SELECT id FROM experiment WHERE $out_dataset_ids)) ORDER BY id ASC) To '${output_dir_path}/unit.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: news.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM news ORDER BY id ASC) To '${output_dir_path}/news.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: prefix.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM prefix ORDER BY id ASC) To '${output_dir_path}/prefix.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: rss_message.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb $DATABASE_NAME <<EOF
  \copy (SELECT * FROM rss_message ORDER BY id ASC) To '${output_dir_path}/rss_message.csv' With (FORMAT CSV, HEADER)
EOF
