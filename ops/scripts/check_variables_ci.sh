#!/bin/bash
# check_variables_ci.sh
# Checks that all required GitLab project variables are set in the environment (for CI/CD)
# if run locally, this script will compare against local variables in .env and .secrets, this would be usually done for testing / debugging the script itself. To locally check missing variables from the gitlab environments, use check_variables_local.sh instead

# if running this script locally and not in a ci/cd gitlab env, then source the .env and .secrets to populate variables
if [[ -z "$CI_JOB_TOKEN" ]]; then
  [ -f .env ] && source .env
  [ -f .secrets ] && source .secrets
fi

# Config
VARIABLES_MD_PATH="docs/variables.md"

# Remove argument parsing; use only $GIGADB_ENV to determine environment
if [[ -n "$GIGADB_ENV" ]]; then
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

# Function: parse_required_variables
# Extracts required project-level variable names from the variables documentation file
parse_required_variables() {
  awk '/^\| [A-Za-z0-9_]+[ ]*\|/ { gsub(/^\| /, ""); gsub(/ .*/, ""); print $1 }' "$VARIABLES_MD_PATH" | grep -v '^Variable$'
}

# Function: check_env_variables
# Checks if required variables are set in the environment
check_env_variables() {
  required_vars=( $(parse_required_variables) )
  echo "[DEBUG] Required variables parsed from $VARIABLES_MD_PATH:" >&2
  for var in "${required_vars[@]}"; do
    echo "  $var" >&2
  done

  missing_vars=()
  for req in "${required_vars[@]}"; do
    # Consider variable missing if it is unset or set to an empty string
    if [[ -z "${!req+x}" || -z "${!req}" ]]; then
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
  echo "Checking required variables in environment (environment: $ENVIRONMENT):"
  check_env_variables
  echo "Done"
  exit $?
fi