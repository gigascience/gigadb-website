# README GENERATOR TOOL

## Notes

* Consider PSR-12 code standard via PHP-codesniffer - install tools into PHPStorm
* Using files-url-updater as a guide for Yii2 template

1. Create new branch
```
git checkout -b readme-generator develop
```

2. Create docker-compose.yml file

3. Create Yii2 basic project template
```
# Create template in a directory called basic
docker-compose run --rm tool composer create-project --prefer-dist yiisoft/yii2-app-basic basic
```

4. Copy relevant Yii2 template files from basic directory
```
cp -R basic/config basic/controllers basic/models basic/runtime basic/tests .
cp -R basic/sql basic/vendor basic/codeception.yml  basic/composer.json .
cp -R basic/composer.lock basic/yii basic/yii.bat .
```

5. Create .gitignore file

6. Test yii installation
```
$ docker-compose run --rm tool /app/yii
Creating readme-generator_generator_run ... done
This is Yii version 2.0.47.
```

7. Create `curators` directory to map to /home/curators for readme files

8. Create ReadmeGeneratorController class

9. Use model classes from file-worker for relational object mapping. If model
   classes are missing, create them using Gii tool:
```
# Check gii command line tool is working
$ docker-compose run --rm tool /app/yii gii

# Get help on how to gii/model sub command
$ docker-compose run --rm tool /app/yii help gii/model

# Requires local gigadb database from repo root
$ ./up.sh

# Generate model class from a table
$ docker-compose run --rm tool /app/yii gii/model --tableName=dataset_author --modelClass=DatasetAuthor
$ docker-compose run --rm tool /app/yii gii/model --tableName=author --modelClass=Author
$ docker-compose run --rm tool /app/yii gii/model --tableName=dataset_type --modelClass=DataType
```

10. Run create read me file function in controller
```
$ docker-compose run --rm tool /app/yii readme/create --doi 100142

# For saving readme file
$ docker-compose run --rm tool /app/yii readme/create --doi=100142 --outdir=/home/curators
```

11. Create and run a container to access its bash shell
```
$ docker-compose run tool sh
```

13. Run functional test:
```
$ docker-compose run --rm tool ./vendor/bin/codecept run tests/functional
```

14. Update composer packages will create a `composer.lock`. This file goes into
version control so that the project is locked to these specific versions of the
dependency and all developers will therefore be using. A `composer.lock` file
is required for autoloading to work from file-worker model classes.
```
$ docker-compose run --rm tool composer update
```

12. List functionality required for creating readme files for datasets

* Take DOI as a parameter to determine what dataset to create README
* Test mode will connect with local database service, use 100142
* Test in dev environment with latest database backup using doi 100314, 100310
* Default is to print readme to standard output
* Use flag --outdir to write readme to file in /home/curators
* Create unit test
* Create functional test to check script outputs a file
