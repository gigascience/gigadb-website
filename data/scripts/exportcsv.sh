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

OPTIND=1
while getopts ":fsVG" opt; do
  case $opt in
  f)
    edebug "Setting input file: $3"
    INPUT_FILE=$3
    if ! test -f "$INPUT_FILE"; then
      eerror "${INPUT_FILE} does not exist!!"
    fi
    ;;
  s)
    verbosity=$silent_lvl
    edebug "-s specified: Silent mode"
    ;;
  V)
    verbosity=$inf_lvl
    edebug "-V specified: Verbose mode"
    ;;
  G)
    verbosity=$dbg_lvl
    edebug "-G specified: Debug mode"
    ;;
  esac
done

#########################
# Set up and sanity check
#########################

# Initialise variables
DATASET_IDS=('41' '27' '58' '196' '40')

# Set working directory to this script directory
WD=$(cd -P -- "$(dirname -- "$0")" && pwd -P)

# Project directory
PROJECT_DIR="${WD%/*}"

# Input directory
FILE_DIR="${WD%/*}"

# Output directory
#OUTPUT_DIR="${WD%/*}/output"
OUTPUT_DIR="${OUTPUT_DIR}"

# Check directories
einfo "PROJECT_DIR: "$PROJECT_DIR
einfo "WORKING DIR: "$WD
einfo "OUTPUT_DIR: "$OUTPUT_DIR
einfo "DOMAIN NAME: ${DOMAIN_NAME}"


OUTPUT_DIR="/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/"


##############
# Main program
##############

# Need to run list of psl commands to export CSV from postgresql database tables


output_sql_condition1() {
  foo=""
  pos=$(( ${#DATASET_IDS[*]} - 1 ))
  last=${DATASET_IDS[$pos]}

  for ID in "${DATASET_IDS[@]}"
  do
    bar="id = $ID"

    if [[ $ID == "$last" ]]
    then
       foo=$foo" ${bar}"
       echo "$foo"
    else
      foo=$foo" ${bar} or"
    fi
  done
}

out_ids=$(output_sql_condition1)

echo $out_ids

output_sql_condition2() {
  foo=""
  pos=$(( ${#DATASET_IDS[*]} - 1 ))
  last=${DATASET_IDS[$pos]}

  for ID in "${DATASET_IDS[@]}"
  do
    bar="dataset_id = $ID"

    if [[ $ID == "$last" ]]
    then
       foo=$foo" ${bar}"
       echo "$foo"
    else
      foo=$foo" ${bar} or"
    fi
  done
}

out_dataset_ids=$(output_sql_condition2)

echo $out_dataset_ids

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from dataset where $out_ids) To '${OUTPUT_DIR}dataset.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from publisher where id in (select publisher_id from dataset where $out_ids)) To '${OUTPUT_DIR}publisher.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from manuscript where $out_dataset_ids) To '${OUTPUT_DIR}manuscript.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from dataset_type where $out_dataset_ids) To '${OUTPUT_DIR}dataset_type.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from "type" where id in (select type_id from dataset_type where $out_dataset_ids)) To '${OUTPUT_DIR}type.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from external_link where $out_dataset_ids) To '${OUTPUT_DIR}external_link.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from "external_link_type" where id in (select external_link_type_id from external_link where $out_dataset_ids)) To '${OUTPUT_DIR}external_link_type.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from dataset_funder where $out_dataset_ids) To '${OUTPUT_DIR}dataset_funder.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from funder_name where id in (select funder_id from dataset_funder where $out_dataset_ids)) To '${OUTPUT_DIR}funder_name.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from link where $out_dataset_ids) To '${OUTPUT_DIR}link.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from dataset_log where $out_dataset_ids) To '${OUTPUT_DIR}dataset_log.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from curation_log where $out_dataset_ids) To '${OUTPUT_DIR}curation_log.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from dataset_project where $out_dataset_ids) To '${OUTPUT_DIR}dataset_project.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from project where id in (select project_id from dataset_project where $out_dataset_ids)) To '${OUTPUT_DIR}project.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from image where id in (select image_id from dataset where $out_ids)) To '${OUTPUT_DIR}image.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from dataset_attributes where $out_dataset_ids) To '${OUTPUT_DIR}dataset_attributes.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from relation where $out_dataset_ids) To '${OUTPUT_DIR}relation.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from relationship where id in (select relationship_id from relation where $out_dataset_ids)) To '${OUTPUT_DIR}relationship.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from sample_rel where relationship_id in (select relationship_id from relation where $out_dataset_ids)) To '${OUTPUT_DIR}sample_rel.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from dataset_author where $out_dataset_ids) To '${OUTPUT_DIR}dataset_author.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from gigadb_user where id in (select submitter_id from dataset where $out_ids) or id in (select curator_id from dataset where $out_ids)) To '${OUTPUT_DIR}gigadb_user.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from author where id in (select author_id from dataset_author where $out_dataset_ids)) To '${OUTPUT_DIR}author.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from "search" where user_id in (select submitter_id from dataset where $out_ids) or id in (select curator_id from dataset where $out_ids)) To '${OUTPUT_DIR}search.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from dataset_sample where $out_dataset_ids) To '${OUTPUT_DIR}dataset_sample.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from sample where id in (select sample_id from dataset_sample where $out_dataset_ids)) To '${OUTPUT_DIR}sample.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from species where id in (select species_id from sample where id in (select sample_id from dataset_sample where $out_dataset_ids))) To '${OUTPUT_DIR}species.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from alternative_identifiers where sample_id in (select sample_id from dataset_sample where $out_dataset_ids)) To '${OUTPUT_DIR}alternative_identifiers.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from extdb where id in (select extdb_id from alternative_identifiers where sample_id in (select sample_id from dataset_sample where $out_dataset_ids))) To '${OUTPUT_DIR}extdb.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from experiment where $out_dataset_ids) To '${OUTPUT_DIR}experiment.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from sample_experiment where experiment_id in (select id from experiment where $out_dataset_ids)) To '${OUTPUT_DIR}sample_experiment.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from sample_attribute where sample_id in (select sample_id from dataset_sample where $out_dataset_ids)) To '${OUTPUT_DIR}sample_attribute.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from attribute where id in (select attribute_id from sample_attribute where sample_id in (select sample_id from dataset_sample where $out_dataset_ids))) To '${OUTPUT_DIR}attribute.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from file where $out_dataset_ids) To '${OUTPUT_DIR}file.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from file_format where id in (select format_id from file where $out_dataset_ids)) To '${OUTPUT_DIR}file_format.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from file_type where id in (select type_id from file where $out_dataset_ids)) To '${OUTPUT_DIR}file_type.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from file_attributes where file_id in (select id from file where $out_dataset_ids)) To '${OUTPUT_DIR}file_attributes.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from file_relationship where file_id in (select id from file where $out_dataset_ids)) To '${OUTPUT_DIR}file_relationship.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from file_sample where file_id in (select id from file where $out_dataset_ids)) To '${OUTPUT_DIR}file_sample.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from file_experiment where file_id in (select id from file where $out_dataset_ids)) To '${OUTPUT_DIR}file_experiment.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from exp_attributes where exp_id in (select id from experiment where $out_dataset_ids)) To '${OUTPUT_DIR}exp_attributes.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from unit where id in (select units_id from exp_attributes where exp_id in (select id from experiment where $out_dataset_ids))) To '${OUTPUT_DIR}unit.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from news) To '${OUTPUT_DIR}news.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from prefix) To '${OUTPUT_DIR}prefix.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from rss_message) To '${OUTPUT_DIR}rss_message.csv' With (FORMAT CSV, HEADER)
EOF

PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb gigadbv3_20200210 <<EOF
\copy (select * from search) To '${OUTPUT_DIR}search.csv' With (FORMAT CSV, HEADER)
EOF



##############

# Read file containing URLs line by line
#while read -r line; do
#
#  einfo "Processing: ${line}"
#  str="${line/$DOMAIN_NAME/}"
#  edebug "Removed domain name: ${str}"
#
#  # Set backslash as delimiter
#  IFS='\/'
#  # Read tokens into strarr string array
#  read -a strarr <<<"${str}"
#
#  # Process URL
#  for ((n = 0; n < ${#strarr[*]}; n++)); do
#    edebug "Working on: ${strarr[n]}"
#
#    if [ $n -eq 0 ]; then
#      dir="$OUTPUT_DIR/${strarr[n]}"
#
#      # Wait 5 secs
#      sleep 5
#
#      if [ ! -d "${dir}" ]; then
#        einfo "Creating directory: ${dir}"
#        mkdir -p "${dir}"
#      else
#        edebug "${strarr[n]} directory already present"
#      fi
#
#    elif [ $n -eq $((${#strarr[*]} - 1)) ]; then
#      einfo "cURL downloading file: ${line}"
#      einfo "Output file: ${dir}/${strarr[n]}"
#      response=$(curl --silent --show-error --fail -w "%{http_code}\n" \
#        -H "Host: variant-spark.s3-ap-southeast-2.amazonaws.com" \
#        "${line}" --output "${dir}/${strarr[n]}")
#
#      # Check cURL response code
#      if [ "${response}" == "200" ]; then
#        eok ${response}
#      else
#        ecrit "Problem with downloading: ${strarr[n]}"
#      fi
#
#
#    else
#      dir="${dir}/${strarr[n]}"
#
#      if [ ! -d "${dir}" ]; then
#        einfo "Creating directory: ${dir}"
#        mkdir -p "${dir}"
#      else
#        edebug "${strarr[n]} directory already present"
#      fi
#    fi
#  done
#
#done < $INPUT_FILE
