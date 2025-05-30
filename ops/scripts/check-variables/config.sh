VARIABLES_MD_PATH="docs/variables.md"
GITLAB_API_URL="https://gitlab.com/api/v4"
DEBUG="false"

token="$GITLAB_PRIVATE_TOKEN"
project_id="$GITLAB_PROJECT_ID"

# Check for required environment variables
if [[ -z "$token" ]]; then
  echo "Error: GITLAB_PRIVATE_TOKEN is not set. Aborting." >&2
  exit 1
fi
if [[ -z "$project_id" ]]; then
  echo "Error: GITLAB_PROJECT_ID is not set. Aborting." >&2
  exit 1
fi