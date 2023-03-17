#!/usr/bin/env bash

# source .env if existing, otherwise create a new from default example then source it
function makeDotEnv () {
  mdeBaseDir=$1

  if [ -f  $mdeBaseDir/.env ];then
      echo "An .env file is present"
  elif [ -f  $mdeBaseDir/config-sources/env.example ];then
    echo "An .env file wasn't present, creating a new one from the default example"
    cp  $mdeBaseDir/config-sources/env.example  $mdeBaseDir/.env
  else
      echo "Neither .env file or default example were present, generating one on the fly"
      if [[ $(uname -o) == Darwin ]];then
        currentEnv=dev
        username=$(git config --get user.name | cut -d" " -f1 | tr '[:upper:]' '[:lower:]')
        repoName="$username-gigadb-website"
        ciProjectUrl="https://gitlab.com/api/v4/projects/gigascience/forks/$repoName/"
        projectVariablesUrl="https://gitlab.com/api/v4/projects/gigascience%2Fforks%2F$repoName/variables"
      else
        currentEnv="$CI_ENVIRONMENT_SLUG"
        repoName="$CI_PROJECT_TITLE"
        ciProjectUrl="https://gitlab.com/api/v4/projects/$CI_PROJECT_PATH"
        projectVariablesUrl="https://gitlab.com/api/v4/projects/gigascience%2Fforks%2F$repoName/variables"
      fi

      echo "REPO_NAME=$repoName" > .env
      {
        echo "GITLAB_PRIVATE_TOKEN=replace-me"
        echo "GIGADB_ENV=$currentEnv"
        echo "CI_PROJECT_URL=$ciProjectUrl"
        echo "PROJECT_VARIABLES_URL=$projectVariablesUrl"
        echo 'GROUP_VARIABLES_URL="https://gitlab.com/api/v4/groups/gigascience/variables?per_page=100"'
        echo 'MISC_VARIABLES_URL="https://gitlab.com/api/v4/projects/gigascience%2Fcnhk-infra/variables"'
        echo 'FORK_VARIABLES_URL="https://gitlab.com/api/v4/groups/3501869/variables"'
      } >> .env

  fi
  echo "Sourcing .env"
  source "$mdeBaseDir/.env"
}

# generate, if not existing yet, .secrets file populated with key and values from Gitlab variables. Then source .secrets.
function makeDotSecrets () {
  mdsBaseDir=$1
  accessToken=${CI_BUILD_TOKEN:-$GITLAB_PRIVATE_TOKEN}

  if ! [ -s ./.secrets ];then

      if [ "$accessToken" == "replace-me" ];then
        echo -e "\033[31m ! Replace the value of GITLAB_PRIVATE_TOKEN in .env, then try again\033[0m"
        return
      fi

      # deal with special case we are in Upstream
      if [[ $CI_PROJECT_URL == "https://gitlab.com/gigascience/upstream/gigadb-website" ]];then
        PROJECT_VARIABLES_URL="https://gitlab.com/api/v4/projects/gigascience%2Fupstream%2Fgigadb-website/variables"
      fi

      echo "Retrieving variables from ${GROUP_VARIABLES_URL}"
      curl -s --header "PRIVATE-TOKEN: $accessToken" "${GROUP_VARIABLES_URL}" | jq -r '.[] | select(.key != "ANALYTICS_PRIVATE_KEY") | .key + "=" + .value' > "$mdsBaseDir/.group_var"

      if [[ $CI_PROJECT_URL != "https://gitlab.com/gigascience/upstream/gigadb-website" ]];then
        echo "Retrieving variables from ${FORK_VARIABLES_URL}"
        curl -s --header "PRIVATE-TOKEN: $accessToken" "${FORK_VARIABLES_URL}?per_page=100" | jq -r '.[] | select(.key != "ANALYTICS_PRIVATE_KEY") | .key + "=" + .value' > "$mdsBaseDir/.fork_var"
      fi

      echo "Retrieving variables from ${PROJECT_VARIABLES_URL}"
      curl -s --header "PRIVATE-TOKEN: $accessToken" "${PROJECT_VARIABLES_URL}?per_page=100&page=1"  > "$mdsBaseDir/.project_var_raw1"
      curl -s --header "PRIVATE-TOKEN: $accessToken" "${PROJECT_VARIABLES_URL}?per_page=100&page=2"  > "$mdsBaseDir/.project_var_raw2"
      jq -s 'add' "$mdsBaseDir/.project_var_raw1" "$mdsBaseDir/.project_var_raw2" > "$mdsBaseDir/.project_vars.json"
      cat "$mdsBaseDir/.project_vars.json" | jq --arg ENVIRONMENT $GIGADB_ENV -r '.[] | select( .environment_scope == $ENVIRONMENT or .environment_scope == "*") | select(.key | test("key|ca|pem|cert";"i") | not ) |.key + "=" + .value' > "$mdsBaseDir/.project_var"

      echo "Retrieving variables from ${MISC_VARIABLES_URL}"
      curl -s --header "PRIVATE-TOKEN: $accessToken" "${MISC_VARIABLES_URL}?per_page=100" | jq --arg ENVIRONMENT $GIGADB_ENV -r '.[] | select(.environment_scope == "*" or .environment_scope == $ENVIRONMENT ) | select(.key | test("sftp_") ) | .key + "=" + .value' > "$mdsBaseDir/.misc_var"

      cat "$mdsBaseDir/.group_var" "$mdsBaseDir/.fork_var" "$mdsBaseDir/.project_var" "$mdsBaseDir/.misc_var" > "$mdsBaseDir/.secrets" && rm "$mdsBaseDir/.group_var" && rm "$mdsBaseDir/.fork_var" && rm "$mdsBaseDir/.project_var" && rm "$mdsBaseDir/.misc_var" && rm "$mdsBaseDir/.project_var_raw1" && rm "$mdsBaseDir/.project_var_raw2" && rm "$mdsBaseDir/.project_vars.json"
      echo "# Some help about this file in ops/configuration/variables/secrets-sample" >> .secrets
  fi
  echo "Sourcing secrets"
  source "$mdsBaseDir/.secrets"
}

# generate config file TARGET from template SOURCE by interpolating placeholders with VARS
function makeConfigs () {
  SOURCE=$1
  TARGET=$2
  VARS=$3
  envsubst $VARS < $SOURCE > $TARGET
}

