# Function: fetch_project_variables
# Uses GITLAB_API_TOKEN and GITLAB_PROJECT_ID to fetch variables from GitLab API
fetch_project_variables() {
  local page=1
  local per_page=100
  local all_vars="[]"
  while :; do
    response=$(curl --silent --fail --show-error --header "PRIVATE-TOKEN: $token" \
      --header "Accept: application/json" \
      "$GITLAB_API_URL/projects/${project_id}/variables?per_page=$per_page&page=$page")
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