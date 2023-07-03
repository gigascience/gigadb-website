#!/usr/bin/env bash

PATH=/usr/local/bin:$PATH
export PATH

currentPath=$(pwd)
if [[ $(uname -n) =~ compute ]];then
  uploadDir=${1:-"$currentPath/uploadDir"}
  cd /home/centos
  . .bash_profile

  if [[ $uploadDir != "/home/centos/uploadDir" ]];then
    mv $uploadDir/* /home/centos/uploadDir/
  fi

  docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c 'drop trigger if exists file_finder_trigger on file RESTRICT'
  docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c 'drop trigger if exists sample_finder_trigger on sample RESTRICT'
  docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c 'drop trigger if exists dataset_finder_trigger on dataset RESTRICT'

  docker run --rm -v /home/centos/uploadDir:/tool/uploadDir -v /home/centos/uploadLogs:/tool/logs registry.gitlab.com/$GITLAB_PROJECT/production_xls_uploader:$GIGADB_ENV ./run.sh

  docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c 'create trigger file_finder_trigger after insert or update or delete or truncate on file for each statement execute procedure refresh_file_finder()'
  docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c 'create trigger sample_finder_trigger after insert or update or delete or truncate on sample for each statement execute procedure refresh_sample_finder()'
  docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c 'create trigger dataset_finder_trigger after insert or update or delete or truncate on dataset for each statement execute procedure refresh_dataset_finder()'

  if [[ $uploadDir != "/home/centos/uploadDir" ]];then
    mv /home/centos/uploadLogs/* "$uploadDir/" || true
  fi
  echo "Done."
else
  mkdir -p logs
  docker-compose run --rm pg_client -c 'drop trigger if exists file_finder_trigger on file RESTRICT'
  docker-compose run --rm pg_client -c 'drop trigger if exists sample_finder_trigger on sample RESTRICT'
  docker-compose run --rm pg_client -c 'drop trigger if exists dataset_finder_trigger on dataset RESTRICT'

  docker-compose run --rm uploader ./run.sh

  docker-compose run --rm pg_client -c 'create trigger file_finder_trigger after insert or update or delete or truncate on file for each statement execute procedure refresh_file_finder()'
  docker-compose run --rm pg_client -c 'create trigger sample_finder_trigger after insert or update or delete or truncate on sample for each statement execute procedure refresh_sample_finder()'
  docker-compose run --rm pg_client -c 'create trigger dataset_finder_trigger after insert or update or delete or truncate on dataset for each statement execute procedure refresh_dataset_finder()'
fi


