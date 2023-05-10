#!/usr/bin/env bash

# Stop script upon error
set -e

PATH=/usr/local/bin:$PATH
export PATH

# Parse command line parameters
while [[ $# -gt 0 ]]; do
    case "$1" in
    --manuscript-id)
        manuscriptid=$2
        shift
        ;;
    *)
        echo "Invalid option: $1"
        exit 1  ## Could be optional.
        ;;
    esac
    shift
done

if [[ $(uname -n) =~ compute ]];then
  . /home/centos/.bash_profile
  docker run --rm -v /home/centos/dropboxGenerator:/app/dropboxGenerator registry.gitlab.com/$GITLAB_PROJECT/production_tool:$GIGADB_ENV /app/createAuthorDropbox.sh --manuscript-id "$manuscript"
else
  # Create Wasabi user account for author based on manuscript identifier
  authorUsername="author-${manuscriptid}"
  docker-compose run --rm tool /app/yii wasabi-user/create --username "${authorUsername}"
  echo "Created Wasabi user account for author: ${authorUsername}"
  
  # Create access key for author 
  docker-compose run --rm tool /app/yii wasabi-user/create-access-key --username "${authorUsername}" > out.txt
  gsed 's/\x1b\[[0-9;]*m//g' out.txt > "${authorUsername}".txt
  rm out.txt
  echo "Saved author's access key and secret in ${authorUsername}.txt"
  

  # Create bucket using bucket name
  bucketName="bucket-${manuscriptid}"
  docker-compose run --rm tool /app/yii wasabi-bucket/create --bucketName "${bucketName}"
  echo "Created bucket ${bucketName} for ${authorUsername}"
  
  # Create user policy for author
  policyArn=$(docker-compose run --rm tool /app/yii wasabi-policy/create-author-policy --username "${authorUsername}")
  echo "Created policy ${policyArn}"
  
  # Attach policy to user
  docker-compose run --rm tool /app/yii wasabi-policy/attach-to-user --username "${authorUsername}" --policy-arn "${policyArn}"
  echo "Attached policy ${policyArn} to ${authorUsername}"
fi
