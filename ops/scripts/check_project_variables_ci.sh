#!/bin/bash
# check_project_variables.sh
# Fetches existing GitLab project variables using the GitLab API

# set -x

# Config
VARIABLES_MD_PATH="docs/vars.md"
GITLAB_API_URL="https://gitlab.com/api/v4"
token=$CI_JOB_TOKEN

# Check if jq is installed
if ! command -v jq >/dev/null 2>&1; then
  echo "Error: jq is not installed. Please install jq to use this script." >&2
  exit 2
fi

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

  local page=1
  local per_page=100
  local all_vars="[]"
  local next_page=1

  while [[ $next_page -ne 0 ]]; do
    response=$(curl --silent --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" \
      --header "Accept: application/json" \
      --write-out "\n%{http_code}\n%{redirect_url}\n%{size_download}\n%{content_type}\n%{response_code}\n%{url_effective}\nX-Next-Page: %{header:X-Next-Page}\n" \
      "$GITLAB_API_URL/projects/${GITLAB_PROJECT_ID}/variables?per_page=$per_page&page=$page" -D -)

    # Extract headers and body
    body=$(echo "$response" | sed '/^X-Next-Page:/q')
    x_next_page=$(echo "$response" | grep -i '^X-Next-Page:' | awk '{print $2}' | tr -d '\r')

    # Merge this page's variables into all_vars
    all_vars=$(jq -s 'add' <(echo "$all_vars") <(echo "$body"))

    if [[ -n "$x_next_page" && "$x_next_page" != "0" ]]; then
      page=$x_next_page
      next_page=$x_next_page
    else
      next_page=0
    fi
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
  required_vars=( $(parse_required_variables) )
  api_vars_json=$(fetch_project_variables)
  if ! api_vars=( $(echo "$api_vars_json" | jq -r '.[].key') ); then
    echo "Error: Failed to parse project variables from GitLab API. Response was:" >&2
    echo "$api_vars_json" >&2
    exit 3
  fi

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