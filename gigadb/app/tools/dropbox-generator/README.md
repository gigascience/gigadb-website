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

# Run command to read file in Wasabi bucket
```
$ docker-compose run --rm tool /app/yii wasabi-bucket/read --bucket dbgiga-datasets --filePath "live/pub/10.5524/102001_103000/102304/bar.txt"
```

# Run command to create new user account
```
$ docker-compose run --rm tool /app/yii wasabi-user/creategigadbuser --manuscriptId giga-d-23-00288
```

# Run command to create new bucket
```
$ docker-compose run --rm tool /app/yii wasabi-bucket/create --bucketName bucket-giga-d-23-00288
```

# Run command to create policy
```
$ docker-compose run --rm tool /app/yii wasabi-policy/create-author-policy --username author-giga-d-4-00286

```

### Functional tests

There is a functional test which checks the `actionCreategigadbuser()` function in
`WasabiController`.
```
$ docker-compose run --rm tool ./vendor/bin/codecept run tests/functional

# Run single test
$ docker-compose run --rm tool ./vendor/bin/codecept run tests/functional/WasabiUserCest.php

$ docker-compose run --rm tool ./vendor/bin/codecept run tests/functional/WasabiBucketCest.php

$ docker-compose run --rm tool ./vendor/bin/codecept run tests/functional/WasabiPolicyCest.php

```
