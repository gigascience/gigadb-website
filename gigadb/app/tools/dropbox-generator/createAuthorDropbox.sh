#!/usr/bin/env bash

# Stop script upon error
set -e

PATH=/usr/local/bin:$PATH
export PATH

manuscriptid=''

# Assume this script will execute in dry run mode
dryrun=true
apply=false

# Parse command line parameters
while [[ $# -gt 0 ]]; do
    case "$1" in
    --manuscript-id)
        manuscriptid=$2
        shift
        ;;
    --dry-run)
        dryrun=true
        ;;
    --apply)
        dryrun=false
        apply=true
        ;;
    *)
        echo "Invalid option: $1"
        exit 1  ## Could be optional.
        ;;
    esac
    shift
done

# If running in dry mode then just echo Wasabi API commands
if [[ "$dryrun" = "true" ]] ; then
    echo "Running script in dry run mode..."
    cmd='echo'
elif [[ "$apply" = "true" ]] ; then
    cmd=''
fi

# Check manuscript identifier
if [[ "$manuscriptid" =~ [[:upper:]] ]]; then
    echo -e "Manuscript identifier: ${manuscriptid} cannot contain upper case letters.\nExiting..."
    exit 1
elif [[ -z "${manuscriptid}" ]] ; then
    echo -e "Manuscript identifier has not been provided.\nExiting..."
    exit 1
elif [[ "${manuscriptid}" == *['!'@#\$%^\&*()_+]* ]] ; then
    echo -e "Manuscript identifier should contain special characters.\nExiting..."
    exit 1
fi

# Create Wasabi user account for author based on manuscript identifier
authorUsername="author-${manuscriptid}"
$cmd docker-compose run --rm tool /app/yii wasabi-user/create --username "${authorUsername}" > /dev/null 2>&1
echo "Created Wasabi user account: ${authorUsername}"

# Create access key for author 
$cmd docker-compose run --rm tool /app/yii wasabi-user/create-access-key --username "${authorUsername}" > "${authorUsername}.txt"
echo "Saved author's access key and secret in ${authorUsername}.txt"

# Create bucket using bucket name
bucketName="bucket-${manuscriptid}"
$cmd docker-compose run --rm tool /app/yii wasabi-bucket/create --bucketName "${bucketName}" > /dev/null 2>&1
echo "Created bucket: ${bucketName} for user: ${authorUsername}"

# Create user policy for author
policyArn=$($cmd docker-compose run --rm tool /app/yii wasabi-policy/create-author-policy --username "${authorUsername}")
if [[ "$dryrun" = "true" ]] ; then
    echo "Created policy: policy-${authorUsername}"
else
    echo "Created policy: ${policyArn}"
fi

# Attach policy to user
$cmd docker-compose run --rm tool /app/yii wasabi-policy/attach-to-user --username "${authorUsername}" --policy-arn "${policyArn}" > /dev/null 2>&1
if [[ "$dryrun" = "true" ]] ; then
  policyArn="policy-${authorUsername}"
fi
echo "Attached policy: ${policyArn} to user: ${authorUsername}"

echo "Successfully finished create author dropbox workflow"
exit 1
