#!/bin/bash
set -euo pipefail
IFS=$'\n\t'

# Function to display usage
show_usage() {
  echo "Usage: $0 [options...]
Compares project variables against variables documented in docs/variables.md and finds missing ones.

Options:
  -e, --env <environment>  Specify the environment (e.g., staging, live, dev, CI).
                           Defaults to 'dev' or GIGADB_ENV if set. Only used in combination with --fetch-gitlab.
  --fetch-gitlab           Fetch variables from GitLab API instead of local environment.
                           Requires GITLAB_PRIVATE_TOKEN and PROJECT_VARIABLES_URL to be set.
  -h, --help               Display this help message and exit.

Behavior:
  1. In a CI/CD pipeline:
     - Arguments are ignored.
     - Variables are checked against the current CI job's shell environment.
     - The environment is automatically detected from CI_ENVIRONMENT_NAME.
  2. Running locally (default behavior):
     - Variables are checked against the current local shell environment.
     - This includes variables sourced from .env and .secrets if they exist.
     - Example: ./ops/scripts/check-variables/check_variables.sh
     - Environment argument is ignored.
  3. Running locally with --fetch-gitlab:
     - Variables are fetched from the GitLab project via API for the specified environment.
     - This mode requires GITLAB_PRIVATE_TOKEN and PROJECT_VARIABLES_URL to be set.
     - Example: ./ops/scripts/check-variables/check_variables.sh --fetch-gitlab -e staging

The list of required variables to check against is always parsed from docs/variables.md."
}

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
        show_usage >&2
        echo "Error: '-e|--env' requires a non-empty argument." >&2
        exit 2
      fi
      ;;
    --fetch-gitlab)
      FETCH_GITLAB=true
      shift
      ;;
    -h|--help)
      show_usage
      exit 0
      ;;
    *)
      show_usage >&2
      echo "Unknown option: $1" >&2
      # echo "Usage: $0 [-e|--env <environment>] [--fetch-gitlab]" >&2 # Removed this line
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
    echo "Running this script locally, for the current local shell environment"
    check_shell_variables
  fi
fi

exit $?
