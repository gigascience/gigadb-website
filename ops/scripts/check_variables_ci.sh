#!/bin/bash
set -euo pipefail
IFS=$'\n\t'
# check_variables_ci.sh
# Checks that all required GitLab project variables are set in the environment (for CI/CD)
# if run locally, this script will compare against local variables in .env and .secrets, this would be usually done for testing / debugging the script itself. To locally check missing variables from the gitlab environments, use check_variables_local.sh instead

# if running this script locally and not in a ci/cd gitlab env, then source the .env and .secrets to populate variables
if [[ -z "${CI_JOB_TOKEN-}" ]]; then
  [ -f .env ] && source .env
  [ -f .secrets ] && source .secrets
fi

# Config
VARIABLES_MD_PATH="docs/variables.md"

# Set DEBUG to true to enable debug output
: "${DEBUG:=false}"

if [[ -n "$GIGADB_ENV" ]]; then
  ENVIRONMENT="$GIGADB_ENV"
else
  ENVIRONMENT="dev"
fi

# Function: parse_required_variables
# Extracts **all** variable names from the variables documentation file, variables are expected to exist in this format: `| MY_VAR     | var description   | value   |`, number of white spaces after each field is arbitrary
parse_required_variables() {
  awk '/^\| [A-Za-z0-9_]+[ ]*\|/ { gsub(/^\| /, ""); gsub(/ .*/, ""); print $1 }' "$VARIABLES_MD_PATH" | grep -v '^Variable$'
}

# Function: check_env_variables
# Checks if required variables are set in the environment
check_env_variables() {
  required_vars=( $(parse_required_variables) )
  if [[ "$DEBUG" == "true" ]]; then
    echo "[DEBUG] Required variables parsed from $VARIABLES_MD_PATH:" >&2
    for var in "${required_vars[@]}"; do
      echo "  $var" >&2
    done
  fi

  missing_vars=()
  for req in "${required_vars[@]}"; do
    # Consider variable missing if it is unset or set to an empty string
    if [[ -z "${!req+x}" || -z "${!req}" ]]; then
      missing_vars+=("$req")
    fi
  done

  if [[ ${#missing_vars[@]} -gt 0 ]]; then
    echo "Error: ${#missing_vars[@]} required CI/CD variable(s) are missing or empty in the '$ENVIRONMENT' environment." >&2
    echo "These variables are defined as required in '$VARIABLES_MD_PATH' and are expected to be available in the CI job environment." >&2
    echo "Please ensure they are correctly set and populated in your GitLab CI/CD project or group settings for this environment:" >&2
    for var in "${missing_vars[@]}"; do
      echo "  - $var" >&2
    done
    return 1
  else
    echo "Success: All required CI/CD variables defined in '$VARIABLES_MD_PATH' are present and non-empty in the '$ENVIRONMENT' environment."
    return 0
  fi
}

if [[ "${BASH_SOURCE[0]}" == "$0" ]]; then
  if [[ "$DEBUG" == "true" ]]; then
    echo "Checking required variables in environment (environment: $ENVIRONMENT):"
  fi
  check_env_variables # ENVIRONMENT is globally available
  if [[ "$DEBUG" == "true" ]]; then
    echo "Done"
  fi
  exit $?
fi