#!/bin/bash

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

### verbosity levels
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

# Set verbosity level to debug for logging
verbosity=$dbg_lvl

#########################
# Set up and sanity check
#########################

# Initialise dataset ids FROM command line arguments as an array
DATASET_IDS=("$@")

# Set working directory to this script directory
WD=$(cd -P -- "$(dirname -- "$0")" && pwd -P)

# Project directory
PROJECT_DIR="${WD%/*}"

# Output directory
OUTPUT_DIR="${WD%/*}/dev/"

# Check directories
einfo "PROJECT_DIR: "$PROJECT_DIR
einfo "WORKING DIR: "$WD
einfo "OUTPUT_DIR: "$OUTPUT_DIR

##############
# Main program
##############

output_sql_condition1() {
  foo=""
  pos=$((${#DATASET_IDS[*]} - 1))
  last=${DATASET_IDS[$pos]}

  for ID in "${DATASET_IDS[@]}"; do
    bar="id = $ID"

    if [[ $ID == "$last" ]]; then
      foo=$foo" ${bar}"
      echo "$foo"
    else
      foo=$foo" ${bar} or"
    fi
  done
}

out_ids=$(output_sql_condition1)
edebug "SQL sub-string for SELECT query: ${out_ids}"

output_sql_condition2() {
  foo=""
  pos=$((${#DATASET_IDS[*]} - 1))
  last=${DATASET_IDS[$pos]}

  for ID in "${DATASET_IDS[@]}"; do
    bar="dataset_id = $ID"

    if [[ $ID == "$last" ]]; then
      foo=$foo" ${bar}"
      echo "$foo"
    else
      foo=$foo" ${bar} or"
    fi
  done
}

out_dataset_ids=$(output_sql_condition2)
edebug "SQL sub-string for SELECT query: ${out_dataset_ids}"

einfo "Creating: dataset.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM dataset WHERE $out_ids) To '${OUTPUT_DIR}dataset.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: publisher.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM publisher WHERE id IN (SELECT publisher_id FROM dataset WHERE $out_ids)) To '${OUTPUT_DIR}publisher.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: manuscript.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM manuscript WHERE $out_dataset_ids) To '${OUTPUT_DIR}manuscript.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: dataset_type.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM dataset_type WHERE $out_dataset_ids) To '${OUTPUT_DIR}dataset_type.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: type.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM "type" WHERE id IN (SELECT type_id FROM dataset_type WHERE $out_dataset_ids)) To '${OUTPUT_DIR}type.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: external_link.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM external_link WHERE $out_dataset_ids) To '${OUTPUT_DIR}external_link.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: external_link_type.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM "external_link_type" WHERE id IN (SELECT external_link_type_id FROM external_link WHERE $out_dataset_ids)) To '${OUTPUT_DIR}external_link_type.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: dataset_funder.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM dataset_funder WHERE $out_dataset_ids) To '${OUTPUT_DIR}dataset_funder.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: funder_name.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM funder_name WHERE id IN (SELECT funder_id FROM dataset_funder WHERE $out_dataset_ids)) To '${OUTPUT_DIR}funder_name.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: link.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM link WHERE $out_dataset_ids) To '${OUTPUT_DIR}link.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: dataset_log.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM dataset_log WHERE $out_dataset_ids) To '${OUTPUT_DIR}dataset_log.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: curation_log.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM curation_log WHERE $out_dataset_ids) To '${OUTPUT_DIR}curation_log.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: dataset_project.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM dataset_project WHERE $out_dataset_ids) To '${OUTPUT_DIR}dataset_project.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: project.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM project WHERE id IN (SELECT project_id FROM dataset_project WHERE $out_dataset_ids)) To '${OUTPUT_DIR}project.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: image.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM image WHERE id IN (SELECT image_id FROM dataset WHERE $out_ids)) To '${OUTPUT_DIR}image.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: dataset_attributes.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM dataset_attributes WHERE $out_dataset_ids) To '${OUTPUT_DIR}dataset_attributes.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: relation.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM relation WHERE $out_dataset_ids) To '${OUTPUT_DIR}relation.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: relationship.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM relationship WHERE id IN (SELECT relationship_id FROM relation WHERE $out_dataset_ids)) To '${OUTPUT_DIR}relationship.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: sample_rel.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM sample_rel WHERE relationship_id IN (SELECT relationship_id FROM relation WHERE $out_dataset_ids)) To '${OUTPUT_DIR}sample_rel.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: dataset_author.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM dataset_author WHERE $out_dataset_ids) To '${OUTPUT_DIR}dataset_author.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: gigadb_user.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM gigadb_user WHERE id IN (SELECT submitter_id FROM dataset WHERE $out_ids) or id IN (SELECT curator_id FROM dataset WHERE $out_ids)) To '${OUTPUT_DIR}gigadb_user.csv' With (FORMAT CSV, HEADER)
EOF
# Append 2 rows into gigadb_user.csv to provide test user and admin accounts
echo ",admin@gigadb.org,5a4f75053077a32e681f81daa8792f95,Joe,Bloggs,BGI,admin,true,false,true,,,,,admin@gigadb.org,,EBI" >> ${OUTPUT_DIR}gigadb_user.csv
echo ",user@gigadb.org,5a4f75053077a32e681f81daa8792f95,John,Smith,BGI,user,true,false,true,,,,,user@gigadb.org,,EBI" >> ${OUTPUT_DIR}gigadb_user.csv

einfo "Creating: author.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM author WHERE id IN (SELECT author_id FROM dataset_author WHERE $out_dataset_ids)) To '${OUTPUT_DIR}author.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: search.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM "search" WHERE user_id IN (SELECT submitter_id FROM dataset WHERE $out_ids) or user_id IN (SELECT curator_id FROM dataset WHERE $out_ids)) To '${OUTPUT_DIR}search.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: dataset_sample.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM dataset_sample WHERE $out_dataset_ids) To '${OUTPUT_DIR}dataset_sample.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: sample.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM sample WHERE id IN (SELECT sample_id FROM dataset_sample WHERE $out_dataset_ids)) To '${OUTPUT_DIR}sample.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: species.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM species WHERE id IN (SELECT species_id FROM sample WHERE id IN (SELECT sample_id FROM dataset_sample WHERE $out_dataset_ids))) To '${OUTPUT_DIR}species.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: alternative_identifiers.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM alternative_identifiers WHERE sample_id IN (SELECT sample_id FROM dataset_sample WHERE $out_dataset_ids)) To '${OUTPUT_DIR}alternative_identifiers.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: extdb.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM extdb WHERE id IN (SELECT extdb_id FROM alternative_identifiers WHERE sample_id IN (SELECT sample_id FROM dataset_sample WHERE $out_dataset_ids))) To '${OUTPUT_DIR}extdb.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: experiment.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM experiment WHERE $out_dataset_ids) To '${OUTPUT_DIR}experiment.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: sample_experiment.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM sample_experiment WHERE experiment_id IN (SELECT id FROM experiment WHERE $out_dataset_ids)) To '${OUTPUT_DIR}sample_experiment.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: sample_attribute.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM sample_attribute WHERE sample_id IN (SELECT sample_id FROM dataset_sample WHERE $out_dataset_ids)) To '${OUTPUT_DIR}sample_attribute.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: attribute.csv"
# Attribute id 497 is urltoredirect attribute which is essential for dataset page display
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM attribute WHERE id IN (497) OR id IN (SELECT DISTINCT attribute_id FROM dataset_attributes WHERE $out_dataset_ids) OR id IN (SELECT DISTINCT attribute_id FROM exp_attributes WHERE exp_id IN (SELECT DISTINCT id FROM experiment WHERE $out_dataset_ids)) OR id IN (SELECT DISTINCT attribute_id FROM file_attributes WHERE file_id IN (SELECT DISTINCT id FROM file WHERE $out_dataset_ids)) OR id IN (SELECT DISTINCT attribute_id FROM sample_attribute WHERE sample_id IN (SELECT DISTINCT sample_id FROM dataset_sample WHERE $out_dataset_ids)) ORDER BY id ASC) To '${OUTPUT_DIR}attribute.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: file.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM file WHERE $out_dataset_ids) To '${OUTPUT_DIR}file.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: file_format.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM file_format WHERE id IN (SELECT format_id FROM file WHERE $out_dataset_ids)) To '${OUTPUT_DIR}file_format.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: file_type.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM file_type WHERE id IN (SELECT type_id FROM file WHERE $out_dataset_ids)) To '${OUTPUT_DIR}file_type.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: file_attributes.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM file_attributes WHERE file_id IN (SELECT id FROM file WHERE $out_dataset_ids)) To '${OUTPUT_DIR}file_attributes.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: file_relationship.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM file_relationship WHERE file_id IN (SELECT id FROM file WHERE $out_dataset_ids)) To '${OUTPUT_DIR}file_relationship.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: file_sample.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM file_sample WHERE file_id IN (SELECT id FROM file WHERE $out_dataset_ids)) To '${OUTPUT_DIR}file_sample.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: file_experiment.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM file_experiment WHERE file_id IN (SELECT id FROM file WHERE $out_dataset_ids)) To '${OUTPUT_DIR}file_experiment.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: exp_attributes.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM exp_attributes WHERE exp_id IN (SELECT id FROM experiment WHERE $out_dataset_ids)) To '${OUTPUT_DIR}exp_attributes.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: unit.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM unit WHERE id IN (SELECT units_id FROM exp_attributes WHERE exp_id IN (SELECT id FROM experiment WHERE $out_dataset_ids))) To '${OUTPUT_DIR}unit.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: news.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM news) To '${OUTPUT_DIR}news.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: prefix.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM prefix) To '${OUTPUT_DIR}prefix.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: rss_message.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (SELECT * FROM rss_message) To '${OUTPUT_DIR}rss_message.csv' With (FORMAT CSV, HEADER)
EOF
