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

# Initialise dataset ids from command line arguments as an array
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
  \copy (select * from dataset where $out_ids) To '${OUTPUT_DIR}dataset.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: publisher.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from publisher where id in (select publisher_id from dataset where $out_ids)) To '${OUTPUT_DIR}publisher.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: manuscript.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from manuscript where $out_dataset_ids) To '${OUTPUT_DIR}manuscript.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: dataset_type.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from dataset_type where $out_dataset_ids) To '${OUTPUT_DIR}dataset_type.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: type.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from "type" where id in (select type_id from dataset_type where $out_dataset_ids)) To '${OUTPUT_DIR}type.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: external_link.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from external_link where $out_dataset_ids) To '${OUTPUT_DIR}external_link.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: external_link_type.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from "external_link_type" where id in (select external_link_type_id from external_link where $out_dataset_ids)) To '${OUTPUT_DIR}external_link_type.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: dataset_funder.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from dataset_funder where $out_dataset_ids) To '${OUTPUT_DIR}dataset_funder.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: funder_name.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from funder_name where id in (select funder_id from dataset_funder where $out_dataset_ids)) To '${OUTPUT_DIR}funder_name.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: link.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from link where $out_dataset_ids) To '${OUTPUT_DIR}link.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: dataset_log.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from dataset_log where $out_dataset_ids) To '${OUTPUT_DIR}dataset_log.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: curation_log.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from curation_log where $out_dataset_ids) To '${OUTPUT_DIR}curation_log.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: dataset_project.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from dataset_project where $out_dataset_ids) To '${OUTPUT_DIR}dataset_project.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: project.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from project where id in (select project_id from dataset_project where $out_dataset_ids)) To '${OUTPUT_DIR}project.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: image.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from image where id in (select image_id from dataset where $out_ids)) To '${OUTPUT_DIR}image.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: dataset_attributes.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from dataset_attributes where $out_dataset_ids) To '${OUTPUT_DIR}dataset_attributes.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: relation.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from relation where $out_dataset_ids) To '${OUTPUT_DIR}relation.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: relationship.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from relationship where id in (select relationship_id from relation where $out_dataset_ids)) To '${OUTPUT_DIR}relationship.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: sample_rel.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from sample_rel where relationship_id in (select relationship_id from relation where $out_dataset_ids)) To '${OUTPUT_DIR}sample_rel.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: dataset_author.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from dataset_author where $out_dataset_ids) To '${OUTPUT_DIR}dataset_author.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: gigadb_user.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from gigadb_user where id in (select submitter_id from dataset where $out_ids) or id in (select curator_id from dataset where $out_ids)) To '${OUTPUT_DIR}gigadb_user.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: author.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from author where id in (select author_id from dataset_author where $out_dataset_ids)) To '${OUTPUT_DIR}author.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: search.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from "search" where user_id in (select submitter_id from dataset where $out_ids) or user_id in (select curator_id from dataset where $out_ids)) To '${OUTPUT_DIR}search.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: dataset_sample.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from dataset_sample where $out_dataset_ids) To '${OUTPUT_DIR}dataset_sample.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: sample.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from sample where id in (select sample_id from dataset_sample where $out_dataset_ids)) To '${OUTPUT_DIR}sample.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: species.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from species where id in (select species_id from sample where id in (select sample_id from dataset_sample where $out_dataset_ids))) To '${OUTPUT_DIR}species.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: alternative_identifiers.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from alternative_identifiers where sample_id in (select sample_id from dataset_sample where $out_dataset_ids)) To '${OUTPUT_DIR}alternative_identifiers.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: extdb.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from extdb where id in (select extdb_id from alternative_identifiers where sample_id in (select sample_id from dataset_sample where $out_dataset_ids))) To '${OUTPUT_DIR}extdb.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: experiment.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from experiment where $out_dataset_ids) To '${OUTPUT_DIR}experiment.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: sample_experiment.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from sample_experiment where experiment_id in (select id from experiment where $out_dataset_ids)) To '${OUTPUT_DIR}sample_experiment.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: sample_attribute.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from sample_attribute where sample_id in (select sample_id from dataset_sample where $out_dataset_ids)) To '${OUTPUT_DIR}sample_attribute.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: attribute.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from attribute where id in (select attribute_id from sample_attribute where sample_id in (select sample_id from dataset_sample where $out_dataset_ids))) To '${OUTPUT_DIR}attribute.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: file.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from file where $out_dataset_ids) To '${OUTPUT_DIR}file.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: file_format.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from file_format where id in (select format_id from file where $out_dataset_ids)) To '${OUTPUT_DIR}file_format.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: file_type.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from file_type where id in (select type_id from file where $out_dataset_ids)) To '${OUTPUT_DIR}file_type.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: file_attributes.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from file_attributes where file_id in (select id from file where $out_dataset_ids)) To '${OUTPUT_DIR}file_attributes.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: file_relationship.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from file_relationship where file_id in (select id from file where $out_dataset_ids)) To '${OUTPUT_DIR}file_relationship.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: file_sample.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from file_sample where file_id in (select id from file where $out_dataset_ids)) To '${OUTPUT_DIR}file_sample.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: file_experiment.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from file_experiment where file_id in (select id from file where $out_dataset_ids)) To '${OUTPUT_DIR}file_experiment.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: exp_attributes.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from exp_attributes where exp_id in (select id from experiment where $out_dataset_ids)) To '${OUTPUT_DIR}exp_attributes.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: unit.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from unit where id in (select units_id from exp_attributes where exp_id in (select id from experiment where $out_dataset_ids))) To '${OUTPUT_DIR}unit.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: news.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from news) To '${OUTPUT_DIR}news.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: prefix.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from prefix) To '${OUTPUT_DIR}prefix.csv' With (FORMAT CSV, HEADER)
EOF

einfo "Creating: rss_message.csv"
PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
  \copy (select * from rss_message) To '${OUTPUT_DIR}rss_message.csv' With (FORMAT CSV, HEADER)
EOF
