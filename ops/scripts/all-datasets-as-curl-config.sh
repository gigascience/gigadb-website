#!/usr/bin/env bash

source .env

docker run --rm --env-file ~/db-env registry.gitlab.com/$GITLAB_PROJECT_NAME/production_pgclient:$GIGADB_ENVIRONMENT -c "COPY (select 'url = \"https://staging.gigadb.org/dataset/' || identifier || '\"' as url from dataset where upload_status = 'Published' order by identifier desc) TO STDOUT"

