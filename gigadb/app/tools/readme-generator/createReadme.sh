#!/usr/bin/env bash

# Stop script upon error
set -e

PATH=/usr/local/bin:$PATH
export PATH

# Parse command line parameters
while [[ $# -gt 0 ]]; do
    case "$1" in
    --doi)
        doi=$2
        shift
        ;;
    --outdir)
        outdir=$2
        shift
        ;;
    --wasabi)
        wasabi_upload=true
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
  docker run --rm -v /home/centos/readmeFiles:/app/readmeFiles registry.gitlab.com/$GITLAB_PROJECT/production_tool:$GIGADB_ENV /app/yii readme/create --doi "$doi" --outdir "$outdir"
else
  docker-compose run --rm tool /app/yii readme/create --doi "$doi" --outdir "$outdir"
fi

if [ "$wasabi_upload" ]; then
  echo "Uploading readme file to Wasabi..."
fi
