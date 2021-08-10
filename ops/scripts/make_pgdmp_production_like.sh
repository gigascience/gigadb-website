#!/usr/bin/env bash

# bail out upon error
set -e
# bail out if an unset variable is used
set -u

# Use database variables in .secrets
set -a
source ./.env
source ./.secrets
set +a

# docker-compose executable
if [[ $GIGADB_ENV != "dev" && $GIGADB_ENV != "CI" ]];then
	DOCKER_COMPOSE="docker-compose --tlsverify -H=$REMOTE_DOCKER_HOST -f ops/deployment/docker-compose.production-envs.yml"
else
	DOCKER_COMPOSE="docker-compose"
fi

# create test database if not existing
$DOCKER_COMPOSE run --rm test bash -c "psql -h database -U gigadb -c 'create database production_like'" || true

# create schema for production_like database
$DOCKER_COMPOSE run --rm  application ./protected/yiic migrate to 300000_000000 --connectionID=testdb_production_like --migrationPath=application.migrations.admin --interactive=0
$DOCKER_COMPOSE run --rm  application ./protected/yiic migrate mark 000000_000000 --connectionID=testdb_production_like --interactive=0
$DOCKER_COMPOSE run --rm  application ./protected/yiic migrate --connectionID=testdb_production_like --migrationPath=application.migrations.schema --interactive=0

# Migration scripts are not generated and used to create the production_like
# database since the size of the scripts causes memory execution problems.
# Tables in production_like database are loaded with data using CSV files
$DOCKER_COMPOSE run --rm test bash -c "PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy publisher FROM '/var/www/data/production_like/publisher.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy image FROM '/var/www/data/production_like/image.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy gigadb_user FROM '/var/www/data/production_like/gigadb_user.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy search FROM '/var/www/data/production_like/search.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy extdb FROM '/var/www/data/production_like/extdb.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy type FROM '/var/www/data/production_like/type.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy dataset FROM '/var/www/data/production_like/dataset.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy attribute FROM '/var/www/data/production_like/attribute.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy author FROM '/var/www/data/production_like/author.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy curation_log FROM '/var/www/data/production_like/curation_log.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy unit FROM '/var/www/data/production_like/unit.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy dataset_attributes FROM '/var/www/data/production_like/dataset_attributes.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy dataset_author FROM '/var/www/data/production_like/dataset_author.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy funder_name FROM '/var/www/data/production_like/funder_name.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy dataset_funder FROM '/var/www/data/production_like/dataset_funder.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy dataset_log FROM '/var/www/data/production_like/dataset_log.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy species FROM '/var/www/data/production_like/species.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy sample FROM '/var/www/data/production_like/sample.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy project FROM '/var/www/data/production_like/project.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy dataset_project FROM '/var/www/data/production_like/dataset_project.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy dataset_sample FROM '/var/www/data/production_like/dataset_sample.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy dataset_type FROM '/var/www/data/production_like/dataset_type.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy experiment FROM '/var/www/data/production_like/experiment.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy exp_attributes FROM '/var/www/data/production_like/exp_attributes.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy external_link_type FROM '/var/www/data/production_like/external_link_type.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy external_link FROM '/var/www/data/production_like/external_link.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy file_format FROM '/var/www/data/production_like/file_format.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy file_type FROM '/var/www/data/production_like/file_type.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy file FROM '/var/www/data/production_like/file.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy file_attributes FROM '/var/www/data/production_like/file_attributes.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy file_experiment FROM '/var/www/data/production_like/file_experiment.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy relationship FROM '/var/www/data/production_like/relationship.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy file_relationship FROM '/var/www/data/production_like/file_relationship.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy file_sample FROM '/var/www/data/production_like/file_sample.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy link FROM '/var/www/data/production_like/link.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy manuscript FROM '/var/www/data/production_like/manuscript.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy news FROM '/var/www/data/production_like/news.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy prefix FROM '/var/www/data/production_like/prefix.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy relation FROM '/var/www/data/production_like/relation.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy rss_message FROM '/var/www/data/production_like/rss_message.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy alternative_identifiers FROM '/var/www/data/production_like/alternative_identifiers.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy sample_attribute FROM '/var/www/data/production_like/sample_attribute.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy sample_experiment FROM '/var/www/data/production_like/sample_experiment.csv' delimiter ',' CSV HEADER\" &&
  PGPASSWORD=$GIGADB_PASSWORD psql -U gigadb -h database -p 5432 -d production_like -c \"\copy sample_rel FROM '/var/www/data/production_like/sample_rel.csv' delimiter ',' CSV HEADER\""

# export a binary dump
$DOCKER_COMPOSE run --rm test bash -c "PGPASSWORD=$GIGADB_PASSWORD pg_dump --no-owner -U gigadb -h database -p 5432 -F custom -d production_like -f /var/www/sql/production_like.pgdmp"
