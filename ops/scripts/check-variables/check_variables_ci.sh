#!/bin/bash
set -euo pipefail
IFS=$'\n\t'
# Checks that all required GitLab project variables are set in the environment (for CI/CD)
# if run locally, this script will compare against local variables in .env and .secrets, this would be usually done for testing / debugging the script itself. To locally check missing variables from the gitlab environments, use check_variables_local.sh instead

LOCAL_DIR="$(dirname "$0")"
source "$LOCAL_DIR/config.sh"

# if running this script locally and not in a gitlab pipeline, then source the .env and .secrets to populate variables
if [[ -z "${CI_JOB_TOKEN-}" ]]; then
  [ -f .env ] && source .env
  [ -f .secrets ] && source .secrets
fi

# Retrieve gitlab environment or default to "dev"
if [[ -n "${CI_ENVIRONMENT_NAME:-}" ]]; then
  ENVIRONMENT="$CI_ENVIRONMENT_NAME"
else
  ENVIRONMENT="dev"
fi

source "$LOCAL_DIR/utils/parse_variables_table.sh"

# Function: check_shell_variables
# Checks if required variables are set in the environment
check_shell_variables() {
  : "${VAR_HEADING:="## PROJECT: *-gigadb-website"}"
  required_vars=( $(parse_variables_table "$VAR_HEADING") )
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


if [[ "$DEBUG" == "true" ]]; then
  echo "Checking required variables in environment (environment: $ENVIRONMENT):"
fi
check_shell_variables
if [[ "$DEBUG" == "true" ]]; then
  echo "Done"
fi
exit $?