# README GENERATOR TOOL

## Notes

* Consider PSR-12 code standard via PHP-codesniffer - install tools into PHPStorm

Using files-url-updater as a guide for Yii2 template

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
$ docker-compose run --rm tool ./yii
Creating readme-generator_generator_run ... done
This is Yii version 2.0.47.
```

7. Create `curators` directory to map to /home/curators for readme files

8. Create ReadmeGeneratorController class

9. List functionality required for creating readme files for datasets

* Take DOI as a parameter to determine what dataset to create README
* Test mode will connect with local database service
* Test in dev environment with latest database backup using doi 100314, 100310
* Default is to print readme to standard output
* Use flag --outdir to write readme to file in /home/curators
* Create unit test
* Create functional test to check script outputs a file
