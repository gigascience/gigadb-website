#!/usr/bin/env bash
set -euo pipefail
IFS=$'\n\t'

# Function: compare_variables
# Compares required variables with those fetched from GitLab API and prints missing ones
compare_variables() {
  : "${ENVIRONMENT:=dev}"
  : "${VARIABLES_MD_PATH:=docs/variables.md}"
  : "${DEBUG:=false}"
  : "${api_vars_json:=}"
  : "${VAR_HEADING:="--undefined--"}"
  # Ensure required_vars is an array
  required_vars=("${required_vars[@]:-}")

  if [[ "$DEBUG" == "true" ]]; then
    echo "[DEBUG] Required variables parsed from $VARIABLES_MD_PATH:" >&2
  fi
  for var in "${required_vars[@]}"; do
    if [[ "$DEBUG" == "true" ]]; then
      echo "  $var" >&2
    fi
  done

  # Filter variables by environment_scope
  filtered_api_vars_json=$(echo "$api_vars_json" | jq --arg env "$ENVIRONMENT" '[.[] | select(.environment_scope == "*" or .environment_scope == $env)]')
  # Print the count of variables retrieved
  var_count=$(echo "$filtered_api_vars_json" | jq 'length')
  if [[ "$DEBUG" == "true" ]]; then
    echo "[INFO] Retrieved $var_count variables from GitLab API for environment '$ENVIRONMENT'." >&2
  fi
  if [[ "$DEBUG" == "true" ]]; then
    echo "[DEBUG] Filtered JSON response from GitLab API:" >&2
    echo "$filtered_api_vars_json" | jq '.' >&2
  fi

  if ! api_vars=( $(echo "$filtered_api_vars_json" | jq -r '.[].key') ); then
    echo "Error: Failed to parse project variables from GitLab API. Response was:" >&2
    if [[ "$DEBUG" == "true" ]]; then
      echo "$filtered_api_vars_json" >&2
    fi
    exit 3
  fi

  if [[ "$DEBUG" == "true" ]]; then
    echo "[DEBUG] Variables retrieved from GitLab API (filtered):" >&2
    for var in "${api_vars[@]}"; do
      echo "  $var" >&2
    done
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
