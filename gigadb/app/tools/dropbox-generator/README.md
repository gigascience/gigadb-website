# DROPBOX-GENERATOR TOOL

# Run test command
```
$ docker-compose run --rm tool /app/yii hello/index
```

# Install AWS SDK PHP into composer
```
# Update composer packages
$ docker-compose run --rm tool composer update
```

# Run command to test connection to Wasabi
```
$ docker-compose run --rm tool /app/yii wasabi/read --bucket dbgiga-datasets --filePath "live/pub/10.5524/102001_103000/102304/bar.txt"
```