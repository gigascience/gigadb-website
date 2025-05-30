#!/usr/bin/env bash
set -euo pipefail
IFS=$'\n\t'

# Function: compare_variables
# Compares required variables with those fetched from GitLab API and prints missing ones
compare_variables() {
  : "${ENVIRONMENT:=dev}"
  : "${VARIABLES_MD_PATH:=docs/variables.md}"
  : "${api_vars_json:=}"
  : "${VAR_HEADING:="--undefined--"}"
  # Ensure required_vars is an array
  required_vars=("${required_vars[@]:-}")

  # Filter variables by environment_scope
  filtered_api_vars_json=$(echo "$api_vars_json" | jq --arg env "$ENVIRONMENT" '[.[] | select(.environment_scope == "*" or .environment_scope == $env)]')
  # Print the count of variables retrieved
  var_count=$(echo "$filtered_api_vars_json" | jq 'length')

  if ! api_vars=( $(echo "$filtered_api_vars_json" | jq -r '.[].key') ); then
    echo "Error: Failed to parse project variables from GitLab API. Response was:" >&2
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
    echo "Error: ${#missing_vars[@]} required variable(s) are missing in the '$ENVIRONMENT' environment." >&2
    echo "These variables are defined as required in '$VARIABLES_MD_PATH' under the '$VAR_HEADING' heading." >&2
    echo "Please ensure they are set in your GitLab CI/CD project variables:" >&2
    for var in "${missing_vars[@]}"; do
      echo "$var" >&2
    done
    return 1
  else
    echo "Success: All required variables defined in '$VARIABLES_MD_PATH' are present in the '$ENVIRONMENT' environment."
    return 0
  fi
}
