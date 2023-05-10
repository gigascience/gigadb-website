# DROPBOX-GENERATOR TOOL

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
$ docker-compose run --rm tool /app/yii wasabi-user/create --username author-giga-d-23-00288
```

# Run command to list user accounts
```
$ docker-compose run --rm tool /app/yii wasabi-user/list-users
```

# Run command to delete user account
```
$ docker-compose run --rm tool /app/yii wasabi-user/delete --username author-giga-d-23-00288
```

# Run command to create new bucket
```
$ docker-compose run --rm tool /app/yii wasabi-bucket/create --bucketName bucket-giga-d-23-00288
```
# Run command to list buckets
```
$ docker-compose run --rm tool /app/yii wasabi-bucket/list-buckets
```

# Run command to delete bucket
```
$ docker-compose run --rm tool /app/yii wasabi-bucket/delete --bucketName bucket-giga-d-23-00288
```

# Run command to create policy
```
$ docker-compose run --rm tool /app/yii wasabi-policy/create-author-policy --username author-giga-d-4-00286
```

# Run command to attach policy to user
```
$ docker-compose run --rm tool /app/yii wasabi-policy/attach-to-user --username author-giga-d-23-00288 --policy-arn arn:aws:iam::100000199914:policy/policy-author-giga-d-23-00288
```

# Run bash script to create dropbox
```
$ ./createAuthorDropbox.sh --manuscript-id giga-d-23-00288
giga-d-23-00288
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

# Run functional test to check user dropbox creation workflow
$ docker-compose run --rm tool ./vendor/bin/codecept run tests/functional/WasabiDropboxCest.php
```
