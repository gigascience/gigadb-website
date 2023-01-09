# README GENERATOR TOOL

## Preparation

In the `gigadb-website` repo root directory, spin up the GigaDB application:
```
$ pwd
/path/to/gigadb-website
$ ./up.sh
```

Now change directory to the `readme-generator` folder
```
$ cd gigadb/app/tools/readme-generator
```

Create a `.env` file:
```
$ cp config-sources/env.example .env
```
> Ensure you have provide values for `GITLAB_PRIVATE_TOKEN` and `REPO_NAME`
> variables.

Generate config files:
```
# Generate configuration using variables in .env, GitLab, then exit
$ docker-compose run --rm config
```
> db.php and test_db.php should be present in the `config` directory.

## Using the readme generator tool

The readme information for a dataset can be viewed on standard output using it's
DOI:
```
$ docker-compose run --rm tool /app/yii readme/create --doi 100142
```

Information for the readme is retrieved from the `database` container that was
spun up using the `up.sh` command above. The tool is able to connect to this
container by connecting to the Docker `db-tier` network.

Saving the readme information into a file requires a file path, for example:
```
$ docker-compose run --rm tool /app/yii readme/create --doi=100142 --outdir=/home/curators
```
Since `/home/curators` has been mounted to `runtime/curators` directory in
`docker-compose.yml`, you should find a `readme_100142.txt` created there after
running the above command.

## Tests

### Unit test

The unit test checks custom-written `getAuthors` function in Dataset model class 
which returns the authors of a dataset based on many-to-many mapping between 
dataset and author tables via a junction `dataset_authors` table:
```
$ docker-compose run --rm tool ./vendor/bin/codecept run tests/unit
```

### Functional test

There is a functional test which checks the `actionCreate()` function in 
`ReadmeController`.
```
$ docker-compose run --rm tool ./vendor/bin/codecept run tests/functional
```
