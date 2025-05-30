# Function: fetch_project_variables
# Fetch variables from GitLab API
fetch_project_variables() {
  local page=1
  local per_page=100
  local all_vars="[]"

  : "${gitlab_token:="$GITLAB_PRIVATE_TOKEN"}"
  : "${gitlab_api_url:="$PROJECT_VARIABLES_URL"}"

  if [[ -z "$gitlab_token" ]]; then
    echo "Error: GITLAB_PRIVATE_TOKEN is not defined. Please set the GITLAB_PRIVATE_TOKEN environment variable." >&2
    exit 1
  fi

  if [[ -z "$gitlab_api_url" ]]; then
    echo "Error: GITLAB_API_URL is not defined. Please set the GITLAB_API_URL environment variable." >&2
    exit 1
  fi

  if ! command -v jq >/dev/null 2>&1; then
    echo "Error: jq is not installed. Please install jq to use this script." >&2
    exit 1
  fi

  while :; do
    response=$(curl --silent --fail --show-error --header "PRIVATE-TOKEN: $gitlab_token" \
      --header "Accept: application/json" \
      "$gitlab_api_url?per_page=$per_page&page=$page")
    if [[ -z "$response" ]] || [[ "$response" == "[]" ]]; then
      break
    fi
    # Merge this page's results into all_vars
    all_vars=$(jq -s 'add' <(echo "$all_vars") <(echo "$response"))
    # If fewer than per_page results, we're done
    count=$(echo "$response" | jq 'length')
    if (( count < per_page )); then
      break
    fi
    ((page++))
  done
  echo "$all_vars"
}

# Call the function if the script is executed directly
if [[ "${BASH_SOURCE[0]}" == "$0" ]]; then
  fetch_project_variables
fi