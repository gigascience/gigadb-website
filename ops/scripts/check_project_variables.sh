#!/bin/bash
# check_project_variables.sh
# Fetches existing GitLab project variables using the GitLab API

source .env
source .secrets

# Function: fetch_project_variables
# Uses GITLAB_API_TOKEN and GITLAB_PROJECT_ID to fetch variables from GitLab API
fetch_project_variables() {
  if [[ -z "$GITLAB_PRIVATE_TOKEN" ]]; then
    echo "Error: GITLAB_PRIVATE_TOKEN is not set." >&2
    return 1
  fi
  if [[ -z "$GITLAB_PROJECT_ID" ]]; then
    echo "Error: GITLAB_PROJECT_ID is not set." >&2
    return 1
  fi
  curl --silent --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" \
    "https://gitlab.com/api/v4/projects/${GITLAB_PROJECT_ID}/variables"
}

# Function: parse_required_variables
# Extracts required project-level variable names from docs/variables.md
parse_required_variables() {
  awk '/^\| [A-Za-z0-9_]+[ ]*\|/ { gsub(/^\| /, ""); gsub(/ .*/, ""); print $1 }' docs/variables.md | grep -v '^Variable$'
}

# Function: compare_variables
# Compares required variables with those fetched from GitLab API and prints missing ones
compare_variables() {
  required_vars=( $(parse_required_variables) )
  api_vars=( $(fetch_project_variables | jq -r '.[].key') )

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
  echo "Comparing required variables with those in GitLab project:"
  compare_variables
  echo "Done"
  exit $?
fi