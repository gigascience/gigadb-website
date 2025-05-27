#!/bin/bash
# check_project_variables.sh
# Fetches existing GitLab project variables using the GitLab API

# Function: fetch_project_variables
# Uses GITLAB_API_TOKEN and CI_PROJECT_ID to fetch variables from GitLab API
fetch_project_variables() {
  if [[ -z "$GITLAB_API_TOKEN" ]]; then
    echo "Error: GITLAB_API_TOKEN is not set." >&2
    return 1
  fi
  if [[ -z "$CI_PROJECT_ID" ]]; then
    echo "Error: CI_PROJECT_ID is not set." >&2
    return 1
  fi
  curl --silent --header "PRIVATE-TOKEN: $GITLAB_API_TOKEN" \
    "https://gitlab.com/api/v4/projects/${CI_PROJECT_ID}/variables"
}

# Function: parse_required_variables
# Extracts required project-level variable names from docs/variables.md
parse_required_variables() {
  awk '/^\| [A-Za-z0-9_]+[ ]*\|/ { gsub(/^\| /, ""); gsub(/ .*/, ""); print $1 }' docs/variables.md | grep -v '^Variable$'
}

# ... existing code ...

# testing
if [[ "${BASH_SOURCE[0]}" == "$0" ]]; then
  echo "Extracted required variable names from docs/variables.md:"
  parse_required_variables
fi