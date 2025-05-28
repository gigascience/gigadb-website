#!/bin/bash
# check_project_variables.sh
# Fetches existing GitLab project variables using the GitLab API

# set -x

[ -f .env ] && source .env
[ -f .secrets ] && source .secrets

# Config
VARIABLES_MD_PATH="docs/vars.md"
GITLAB_API_URL="https://gitlab.com/api/v4"
token=$GITLAB_PRIVATE_TOKEN
project_id=$GITLAB_PROJECT_ID

# Check for required environment variables
if [[ -z "$token" ]]; then
  echo "Error: GITLAB_PRIVATE_TOKEN is not set. Aborting." >&2
  exit 1
fi
if [[ -z "$project_id" ]]; then
  echo "Error: GITLAB_PROJECT_ID is not set. Aborting." >&2
  exit 1
fi

# Check if jq is installed
if ! command -v jq >/dev/null 2>&1; then
  echo "Error: jq is not installed. Please install jq to use this script." >&2
  exit 1
fi

# Argument parsing for environment
ENV_ARG=""
while [[ $# -gt 0 ]]; do
  case $1 in
    -e|--env)
      ENV_ARG="$2"
      shift 2
      ;;
    *)
      shift
      ;;
  esac
done

# Determine environment
if [[ -n "$ENV_ARG" ]]; then
  ENVIRONMENT="$ENV_ARG"
elif [[ -n "$GIGADB_ENV" ]]; then
  ENVIRONMENT="$GIGADB_ENV"
else
  ENVIRONMENT="dev"
fi

# Validate environment
case "$ENVIRONMENT" in
  staging|live|dev|CI)
    ;;
  *)
    echo "Error: Invalid environment '$ENVIRONMENT'. Must be one of: staging, live, dev, CI." >&2
    exit 2
    ;;
esac

# Function: fetch_project_variables
# Uses GITLAB_API_TOKEN and GITLAB_PROJECT_ID to fetch variables from GitLab API
fetch_project_variables() {
  local page=1
  local per_page=100
  local all_vars="[]"
  while :; do
    response=$(curl --silent --header "PRIVATE-TOKEN: $token" \
      --header "Accept: application/json" \
      "$GITLAB_API_URL/projects/${project_id}/variables?per_page=$per_page&page=$page")
    # If response is empty or not an array, break
    if [[ -z "$response" ]] || [[ "$response" == "[]" ]]; then
      break
    fi
    # Merge this page's results into all_vars
    all_vars=$(jq -s 'add' <(echo "$all_vars") <(echo "$response"))
    # If fewer than per_page results, we're done
    count=$(echo "$response" | jq 'length')
    if (( count < per_page )); then
      break
    fi
    ((page++))
  done
  echo "$all_vars"
}

# Function: parse_required_variables
# Extracts required project-level variable names from the variables documentation file
parse_required_variables() {
  awk '/^\| [A-Za-z0-9_]+[ ]*\|/ { gsub(/^\| /, ""); gsub(/ .*/, ""); print $1 }' "$VARIABLES_MD_PATH" | grep -v '^Variable$'
}

# Function: compare_variables
# Compares required variables with those fetched from GitLab API and prints missing ones
compare_variables() {
  local ENVIRONMENT="$1"
  required_vars=( $(parse_required_variables) )
  echo "[DEBUG] Required variables parsed from $VARIABLES_MD_PATH:" >&2
  for var in "${required_vars[@]}"; do
    echo "  $var" >&2
  done

  api_vars_json=$(fetch_project_variables)
  # Filter variables by environment_scope
  filtered_api_vars_json=$(echo "$api_vars_json" | jq --arg env "$ENVIRONMENT" '[.[] | select(.environment_scope == "*" or .environment_scope == $env)]')
  # Print the count of variables retrieved
  var_count=$(echo "$filtered_api_vars_json" | jq 'length')
  echo "[INFO] Retrieved $var_count variables from GitLab API for environment '$ENVIRONMENT'." >&2
  echo "[DEBUG] Filtered JSON response from GitLab API:" >&2
  echo "$filtered_api_vars_json" | jq '.' >&2

  if ! api_vars=( $(echo "$filtered_api_vars_json" | jq -r '.[].key') ); then
    echo "Error: Failed to parse project variables from GitLab API. Response was:" >&2
    echo "$filtered_api_vars_json" >&2
    exit 3
  fi

  echo "[DEBUG] Variables retrieved from GitLab API (filtered):" >&2
  for var in "${api_vars[@]}"; do
    echo "  $var" >&2
  done

  missing_vars=()
  for req in "${required_vars[@]}"; do
    found=false
    for api in "${api_vars[@]}"; do
      if [[ "$req" == "$api" ]]; then
        found=true
        break
      fi
    done
    if ! $found; then
      missing_vars+=("$req")
    fi
  done

  if [[ ${#missing_vars[@]} -gt 0 ]]; then
    echo "Missing required variables:" >&2
    for var in "${missing_vars[@]}"; do
      echo "  $var" >&2
    done
    return 1
  else
    echo "All required variables are present."
    return 0
  fi
}

if [[ "${BASH_SOURCE[0]}" == "$0" ]]; then
  echo "Comparing required variables with those in GitLab project (environment: $ENVIRONMENT):"
  compare_variables "$ENVIRONMENT"
  echo "Done"
  exit $?
fi