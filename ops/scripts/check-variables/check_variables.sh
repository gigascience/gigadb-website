#!/bin/bash
set -euo pipefail
IFS=$'\n\t'
# Compares project variables against the specified variables file and finds missing ones
echo "Usage: $0 [-e|--env <environment>] [--fetch-gitlab]"

# Argument parsing
ENV_ARG=""
FETCH_GITLAB=false # Flag to indicate if we should fetch variables from GitLab API

while [[ $# -gt 0 ]]; do
  case $1 in
    -e|--env)
      if [[ -n "${2:-}" && ! "${2}" =~ ^- ]]; then
        ENV_ARG="$2"
        shift 2
      else
        echo "Error: '-e|--env' requires a non-empty argument." >&2
        exit 2
      fi
      ;;
    --fetch-gitlab)
      FETCH_GITLAB=true
      shift
      ;;
    *)
      echo "Unknown option: $1" >&2
      echo "Usage: $0 [-e|--env <environment>] [--fetch-gitlab]" >&2
      exit 2
      ;;
  esac
done

# assumes script is run from project root
VARIABLES_MD_PATH="docs/variables.md"
VAR_HEADING="## PROJECT: *-gigadb-website"

# Determine if running in CI/CD environment
IS_CI=false
if [[ -n "${CI_JOB_TOKEN-}" ]]; then
  IS_CI=true
  FETCH_GITLAB=false # force to false if on gitlab, regardless of arguments
fi

if [[ "$IS_CI" == "false" ]]; then
  echo "Sourcing .env and .secrets"
  [ -f .env ] && source .env
  [ -f .secrets ] && source .secrets
fi

# Determine environment
if [[ "$IS_CI" == "true" && -n "${CI_ENVIRONMENT_NAME:-}" ]]; then
  ENVIRONMENT="$CI_ENVIRONMENT_NAME"
elif [[ -n "$ENV_ARG" ]]; then
  ENVIRONMENT="$ENV_ARG"
elif [[ -n "${GIGADB_ENV:-}" ]]; then
  # Fallback first to the locally defined GIGADB_ENV var
  ENVIRONMENT="$GIGADB_ENV"
else
  ENVIRONMENT="dev" # Default environment
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

LOCAL_DIR="$(dirname "$0")"
source "$LOCAL_DIR/utils/parse_variables_table.sh"

# Function: check_shell_variables
# Checks if required variables are set in the environment
check_shell_variables() {
  required_vars=( $(parse_variables_table) )

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

# The rest of the script will use IS_CI and FETCH_GITLAB to determine its mode of operation.

if [[ "$IS_CI" == "true" ]]; then
  echo "Running this script in CI/CD pipeline for the $ENVIRONMENT environment"
  check_shell_variables
else
  if [[ "$FETCH_GITLAB" == "true" ]]; then
    echo "Running this script locally for the $ENVIRONMENT environment, fetching remote variables from GitLab"
    # If fetching from GitLab, GITLAB_PRIVATE_TOKEN and PROJECT_VARIABLES_URL are required to run the script.
    if [[ -z "$GITLAB_PRIVATE_TOKEN" ]]; then
      echo "Error: GITLAB_PRIVATE_TOKEN is not set. It's required when using '--fetch-gitlab'. Aborting." >&2
      exit 1
    fi
    if [[ -z "$PROJECT_VARIABLES_URL" ]]; then
      echo "Error: PROJECT_VARIABLES_URL is not set. It's required when using '--fetch-gitlab'. Aborting." >&2
      exit 1
    fi

    source "$LOCAL_DIR/utils/fetch_project_variables.sh"
    source "$LOCAL_DIR/utils/compare_variables.sh"

    required_vars=( $(parse_variables_table) )
    api_vars_json=$(fetch_project_variables)
    compare_variables
  else
    echo "Running this script locally, for the locally set variables"
    check_shell_variables
  fi
fi

exit $?
