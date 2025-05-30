#!/bin/bash
set -euo pipefail
IFS=$'\n\t'
# Fetches existing GitLab project variables using the GitLab API and compares them against the specified variables file
# Usage: bash ops/scripts/check_variables_local.sh [-e staging|live|dev|CI]

[ -f .env ] && source .env
[ -f .secrets ] && source .secrets

LOCAL_DIR="$(dirname "$0")"
source "$LOCAL_DIR/config.sh"

gitlab_token="$GITLAB_PRIVATE_TOKEN"
gitlab_api_url="$PROJECT_VARIABLES_URL"

# Check for required environment variables
if [[ -z "$gitlab_token" ]]; then
  echo "Error: GITLAB_PRIVATE_TOKEN is not set. Aborting." >&2
  exit 1
fi
if [[ -z "$gitlab_api_url" ]]; then
  echo "Error: PROJECT_VARIABLES_URL is not set. Aborting." >&2
  exit 1
fi


# Argument parsing
ENV_ARG=""
while [[ $# -gt 0 ]]; do
  case $1 in
    -e|--env)
      ENV_ARG="$2"
      shift 2
      ;;
    *)
      shift
      ;;
  esac
done

# Determine environment
if [[ -n "$ENV_ARG" ]]; then
  ENVIRONMENT="$ENV_ARG"
elif [[ -n "${GIGADB_ENV:-}" ]]; then
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

source "$LOCAL_DIR/utils/fetch_project_variables.sh"
source "$LOCAL_DIR/utils/parse_variables_table.sh"
source "$LOCAL_DIR/utils/compare_variables.sh"

if [[ "${BASH_SOURCE[0]}" == "$0" ]]; then
  if [[ "$DEBUG" == "true" ]]; then
    echo "Comparing required variables with those in GitLab project (environment: $ENVIRONMENT):"
  fi
  var_heading="## PROJECT: *-gigadb-website"
  required_vars=( $(parse_variables_table "$var_heading") )
  api_vars_json=$(fetch_project_variables)
  compare_variables
  if [[ "$DEBUG" == "true" ]]; then
    echo "Done"
  fi
  exit $?
fi