# README GENERATOR TOOL

## Preparation

In the `gigadb-website` repo root directory, spin up the GigaDB application
since we need access to the `database` container for the `gigadb` and 
`gigadb_testdata` databases.
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
$ docker-compose run --rm configure
```
> db.php and test_db.php should be present in the `config` directory. There 
> should be a runtime/curators directory too.

Install Composer dependencies:
```
$  docker-compose run --rm tool composer install 
```

The copying of readme files created by this tool into Wasabi requires Rclone
to be installed on your `dev` machine. This can be done using as follows:
```
# Using Homebrew
$ brew install rclome
# Or using Macports
$ sudo port install rclone
```

> There is an analogous step in the Ansible playbook for the bastion server 
> for installing Rclone on staging and live environments.
> ansible-galaxy install -r ../../../infrastructure/requirements.yml

The create readme tool uses the rclone configuration file from the wasabi
migration tool. Change directory to the `gigadb-website/gigadb/app/tools/wasabi-migration`
directory and make a copy of the `env.example` file called `.env`:
```
$ cd gigadb/app/tools/wasabi-migration
$ cp env.example .env
```

In the new `.env` file, uncomment and provide a value for the
`GITLAB_PRIVATE_TOKEN` variable.

Also provide the name of GitLab project fork in the `REPO_NAME` variable.

You should then be able to create the configuration file for rclone by
executing:
```
$ docker-compose run --rm config
```

Check if the configuration process has worked by looking for the
`config/rclone.conf` file.

## How to test

Ensure you have `bats` installed (e.g: on macOS, you could do `brew install bats-core`
or `port install bats-core`). Then run:
```
# Ensure you are in gigadb/app/tools/readme-generator directory
$ bats tests
 ✓ create readme file
 ✓ create readme file and copy to wasabi
```


## Using readme generator tool

The readme information for a dataset can be viewed on standard output using its
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

## Using readme generator tool via shell wrapper script 

There is a shell script which can also be used to call the readme tool:
```
$ ./createReadme.sh --doi 100142 --outdir /home/curators
```

In the absence of an output directory `outdir` parameter or if the directory
cannot be created then an error message will be displayed:
```
$ ./createReadme.sh --doi 100142 --outdir /home/foo
Cannot save readme file - Output directory does not exist or is not a directory
ERROR: 65
```

An error message is also displayed if a DOI is provided for a dataset that does 
not exist:
```
$ ./create_readme.sh --doi 1
Creating readme_tool_run ... done
Exception 'Exception' with message 'Dataset 1 not found'
```

## Using readme generator tool on Bastion server

Before running the bastion playbook, execute the following command to install
the ansible-rclone role for Ansible in your development environment:
```
$ ansible-galaxy install -r ../../../infrastructure/requirements.yml
```

Log into bastion server
```
# Get public IP address for bastion server
$ terraform output
ec2_bastion_private_ip = "10.88.8.888"
ec2_bastion_public_ip = "88.888.888.888"

# Log into bastion server
$ ssh -i ~/.ssh/your-private-key.pem centos@88.888.888.888
```

Using docker command to access tool:
```
$ docker run --rm -v /home/centos/readmeFiles:/app/readmeFiles registry.gitlab.com/$GITLAB_PROJECT/production_tool:staging /app/yii readme/create --doi 100142 --outdir /app/readmeFiles
```

Use shell script to run readme tool:
```
$ ./createReadme.sh --doi 100142 --outdir /app/readmeFiles
```

In both cases, look in the readmeFiles directory for the readme file that has
been created by the tool.

## Tests

### Unit test

The unit test checks custom-written `getAuthors` function in Dataset model class 
which returns the authors of a dataset based on many-to-many mapping between 
dataset and author tables via a junction `dataset_authors` table:
```
$ docker-compose run --rm tool ./vendor/bin/codecept run tests/unit
```

There's also a unit test to check the ReadmeGenerator component class:
```
$ docker-compose run --rm tool ./vendor/bin/codecept run --debug tests/unit/components/ReadmeGeneratorTest.php
```

### Functional test

There is a functional test which checks the `actionCreate()` function in 
`ReadmeController`.
```
$ docker-compose run --rm tool ./vendor/bin/codecept run tests/functional

# Run single test
$ docker-compose run --rm tool ./vendor/bin/codecept run tests/functional/ReadmeCest.php
```
