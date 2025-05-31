#!/usr/bin/env bash
set -euo pipefail
IFS=$'\n\t'
# Function: fetch_project_variables
# Fetch variables from GitLab API
fetch_project_variables() {
  local page=1
  local per_page=100
  local all_vars="[]"

  if [[ -z "$GITLAB_PRIVATE_TOKEN" ]]; then
    echo "Error: GITLAB_PRIVATE_TOKEN is not defined. Please set the GITLAB_PRIVATE_TOKEN environment variable." >&2
    exit 1
  fi

  if [[ -z "$PROJECT_VARIABLES_URL" ]]; then
    echo "Error: PROJECT_VARIABLES_URL is not defined. Please set the PROJECT_VARIABLES_URL environment variable." >&2
    exit 1
  fi

  if ! command -v jq >/dev/null 2>&1; then
    echo "Error: jq is not installed. Please install jq to use this script." >&2
    exit 1
  fi

  if ! command -v curl >/dev/null 2>&1; then
    echo "Error: curl is not installed. Please install curl to use this script." >&2
    exit 1
  fi

  while :; do
    response=$(curl --silent --fail --show-error --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" \
      --header "Accept: application/json" \
      "$PROJECT_VARIABLES_URL?per_page=$per_page&page=$page")
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

# Call the function if the script is executed directly
if [[ "${BASH_SOURCE[0]}" == "$0" ]]; then
  fetch_project_variables
fi