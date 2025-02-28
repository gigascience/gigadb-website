#!/bin/bash

# set_env_vars.sh
#
# This script sets GitLab CI/CD variables for different environments using the GitLab API.
# Variables are read from a .gitlab-env-vars file in the current directory.
#
# Prerequisites:
#   - .env file with GITLAB_PRIVATE_TOKEN and PROJECT_VARIABLES_URL defined
#   - .gitlab-env-vars file containing variable definitions, of which you can find an example in ops/configuration/variables/.gitlab-env-vars.example
#
# .gitlab-env-vars file format:
#   VARIABLE_NAME=value|environment|visibility
#
# Examples:
#   DB_HOST=localhost|staging|visible
#   DB_PASS=secretpass123|live|masked
#   API_URL=||visible                    # Uses default (*) environment
#   EMPTY_VAR=|staging|visible          # Empty value for staging
#
# Notes:
#   - environment: optional, defaults to '*' (all environments)
#   - visibility: must be either 'visible' or 'masked'
#   - masked variables require a value of at least 8 characters
#   - empty lines and lines starting with # are ignored
#
# Usage:
#   ./set_env_vars.sh

source .env

if [ -z "$GITLAB_PRIVATE_TOKEN" ]; then
  echo "Error: GITLAB_PRIVATE_TOKEN is not set. Check your .env file."
  exit 1
fi

if [ -z "$PROJECT_VARIABLES_URL" ]; then
  echo "Error: PROJECT_VARIABLES_URL is not set. Check your .env file."
  exit 1
fi

# even if PROJECT_VARIABLES_URL is set, make sure REPO_NAME is also set
if [ -z "$REPO_NAME" ]; then
  echo "Error: REPO_NAME is not set. Check your .env file."
  exit 1
fi

if [ ! -f ".gitlab-env-vars" ]; then
  echo "Error: .gitlab-env-vars file not found"
  exit 1
fi

# Read environment variables from .gitlab-env-vars file
declare -A ENV_VARS
while IFS="=" read -r key value || [ -n "$key" ]; do
  # Skip empty lines and comments
  [[ -z "$key" || "$key" =~ ^[[:space:]]*# ]] && continue
  # Trim whitespace from key only (preserve empty values in the value part)
  key=$(echo "$key" | xargs)
  # Remove any leading/trailing whitespace but preserve empty values between pipes
  value=$(echo "$value" | sed 's/^[[:space:]]*//;s/[[:space:]]*$//')
  ENV_VARS["$key"]="$value"
done < ".gitlab-env-vars"

# Loop through and set each variable
for KEY in "${!ENV_VARS[@]}"; do
  # Split on pipe but preserve empty fields
  IFS='|' read -r VALUE VAR_ENV MASKED_STATUS <<< "${ENV_VARS[$KEY]}"

  # Convert visibility settings
  MASKED="false"
  if [ "$MASKED_STATUS" = "masked" ]; then
    MASKED="true"
    # For masked variables, ensure value is not empty and meets minimum length
    if [ -z "$VALUE" ] || [ ${#VALUE} -lt 8 ]; then
      echo "Error: Masked variable $KEY requires a value of at least 8 characters"
      continue
    fi
  fi

  # Set default environment scope if not specified
  ENV_SCOPE="*"
  if [ -n "$VAR_ENV" ]; then
    ENV_SCOPE="$VAR_ENV"
  fi

  # Prepare JSON data
  JSON_DATA="{
    \"key\": \"$KEY\",
    \"value\": \"$VALUE\",
    \"protected\": false,
    \"masked\": $MASKED,
    \"raw\": true,
    \"environment_scope\": \"$ENV_SCOPE\"
  }"

  # Make the API call and capture the response
  RESPONSE=$(curl --fail -sS --request POST "$PROJECT_VARIABLES_URL" \
    --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" \
    --header "Content-Type: application/json" \
    --data "$JSON_DATA" 2>&1)

  if [ $? -eq 0 ]; then
    echo "Successfully set $KEY ($MASKED_STATUS) for environment ${ENV_SCOPE}"
  else
    echo "Error setting $KEY: $RESPONSE"
  fi
done