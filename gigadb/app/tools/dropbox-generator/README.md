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
$ docker-compose run --rm tool /app/yii wasabi-user/create --username author-giga-d-23-00288
object(Aws\Result)#289 (2) {
  ["data":"Aws\Result":private]=>
  array(2) {
    ["User"]=>
    array(5) {
      ["Path"]=>
      string(1) "/"
      ["UserName"]=>
      string(22) "author-giga-d-23-00288"
      ["UserId"]=>
      string(21) "YDGGFQL9DW38U09I9W0E4"
      ["Arn"]=>
      string(53) "arn:aws:iam::100000199914:user/author-giga-d-23-00288"
      ["CreateDate"]=>
      object(Aws\Api\DateTimeResult)#299 (3) {
        ["date"]=>
        string(26) "2023-05-08 05:30:27.822000"
        ["timezone_type"]=>
        int(2)
        ["timezone"]=>
        string(1) "Z"
      }
    }
    ["@metadata"]=>
    array(4) {
      ["statusCode"]=>
      int(200)
      ["effectiveUri"]=>
      string(26) "https://iam.wasabisys.com/"
      ["headers"]=>
      array(9) {
        ["content-length"]=>
        string(3) "445"
        ["content-type"]=>
        string(8) "text/xml"
        ["date"]=>
        string(29) "Mon, 08 May 2023 05:30:27 GMT"
        ["server"]=>
        string(9) "WasabiIAM"
        ["vary"]=>
        string(15) "Accept-Encoding"
        ["x-amzn-requestid"]=>
        string(36) "3e55d596-98de-028c-c233-96d53eefdd4e"
        ["x-wasabi-cluster-id"]=>
        string(9) "us-east-1"
        ["x-wasabi-service"]=>
        string(11) "iam_service"
        ["x-wasabi-service-runtime-id"]=>
        string(12) "69e04db6a36f"
      }
      ["transferStats"]=>
      array(1) {
        ["http"]=>
        array(1) {
          [0]=>
          array(0) {
          }
        }
      }
    }
  }
  ["monitoringEvents":"Aws\Result":private]=>
  array(0) {
  }
}

```

# Run command to list user accounts
```
$ docker-compose run --rm tool /app/yii wasabi-user/list-users
object(Aws\Result)#288 (2) {
  ["data":"Aws\Result":private]=>
  array(3) {
    ["Users"]=>
    array(5) {
      [0]=>
      array(6) {
        ["Path"]=>
        string(1) "/"
        ["UserName"]=>
        string(5) "Peter"
        ["UserId"]=>
        string(21) "K0C4C6WAM8UBI67TN07QC"
        ["Arn"]=>
        string(36) "arn:aws:iam::100000199914:user/Peter"
        ["CreateDate"]=>
        object(Aws\Api\DateTimeResult)#308 (3) {
          ["date"]=>
          string(26) "2023-04-18 14:21:25.000000"
          ["timezone_type"]=>
          int(2)
          ["timezone"]=>
          string(1) "Z"
        }
        ["PasswordLastUsed"]=>
        object(Aws\Api\DateTimeResult)#309 (3) {
          ["date"]=>
          string(26) "2023-04-27 06:39:22.000000"
          ["timezone_type"]=>
          int(2)
          ["timezone"]=>
          string(1) "Z"
        }
      }
      [1]=>
      array(6) {
        ["Path"]=>
        string(1) "/"
        ["UserName"]=>
        string(5) "Chris"
        ["UserId"]=>
        string(21) "CFMS87Q347IPH3581ATXV"
        ["Arn"]=>
        string(36) "arn:aws:iam::100000199914:user/Chris"
        ["CreateDate"]=>
        object(Aws\Api\DateTimeResult)#310 (3) {
          ["date"]=>
          string(26) "2023-04-18 14:24:04.000000"
          ["timezone_type"]=>
          int(2)
          ["timezone"]=>
          string(1) "Z"
        }
        ["PasswordLastUsed"]=>
        object(Aws\Api\DateTimeResult)#311 (3) {
          ["date"]=>
          string(26) "2023-05-02 03:56:20.000000"
          ["timezone_type"]=>
          int(2)
          ["timezone"]=>
          string(1) "Z"
        }
      }
      [2]=>
      array(5) {
        ["Path"]=>
        string(1) "/"
        ["UserName"]=>
        string(15) "giga-d-23-00123"
        ["UserId"]=>
        string(21) "NCXRGXE9DVTVWEYRPUL9L"
        ["Arn"]=>
        string(46) "arn:aws:iam::100000199914:user/giga-d-23-00123"
        ["CreateDate"]=>
        object(Aws\Api\DateTimeResult)#312 (3) {
          ["date"]=>
          string(26) "2023-04-19 04:06:55.000000"
          ["timezone_type"]=>
          int(2)
          ["timezone"]=>
          string(1) "Z"
        }
      }
      [3]=>
      array(5) {
        ["Path"]=>
        string(1) "/"
        ["UserName"]=>
        string(6) "System"
        ["UserId"]=>
        string(21) "EVGERORJRQYYVEXPIH8WU"
        ["Arn"]=>
        string(37) "arn:aws:iam::100000199914:user/System"
        ["CreateDate"]=>
        object(Aws\Api\DateTimeResult)#313 (3) {
          ["date"]=>
          string(26) "2023-05-03 06:27:17.000000"
          ["timezone_type"]=>
          int(2)
          ["timezone"]=>
          string(1) "Z"
        }
      }
      [4]=>
      array(5) {
        ["Path"]=>
        string(1) "/"
        ["UserName"]=>
        string(22) "author-giga-d-23-00288"
        ["UserId"]=>
        string(21) "MOWSBB7DAZL6WMNBVATWD"
        ["Arn"]=>
        string(53) "arn:aws:iam::100000199914:user/author-giga-d-23-00288"
        ["CreateDate"]=>
        object(Aws\Api\DateTimeResult)#314 (3) {
          ["date"]=>
          string(26) "2023-05-08 05:58:35.000000"
          ["timezone_type"]=>
          int(2)
          ["timezone"]=>
          string(1) "Z"
        }
      }
    }
    ["IsTruncated"]=>
    bool(false)
    ["@metadata"]=>
    array(4) {
      ["statusCode"]=>
      int(200)
      ["effectiveUri"]=>
      string(26) "https://iam.wasabisys.com/"
      ["headers"]=>
      array(9) {
        ["content-length"]=>
        string(4) "1515"
        ["content-type"]=>
        string(8) "text/xml"
        ["date"]=>
        string(29) "Mon, 08 May 2023 06:03:01 GMT"
        ["server"]=>
        string(9) "WasabiIAM"
        ["vary"]=>
        string(15) "Accept-Encoding"
        ["x-amzn-requestid"]=>
        string(36) "28f5d38e-83c2-fe29-57d7-a1805cdd7a33"
        ["x-wasabi-cluster-id"]=>
        string(9) "us-east-1"
        ["x-wasabi-service"]=>
        string(11) "iam_service"
        ["x-wasabi-service-runtime-id"]=>
        string(12) "eeb72e47fc9c"
      }
      ["transferStats"]=>
      array(1) {
        ["http"]=>
        array(1) {
          [0]=>
          array(0) {
          }
        }
      }
    }
  }
  ["monitoringEvents":"Aws\Result":private]=>
  array(0) {
  }
}
```

# Run command to delete user account
```
$ docker-compose run --rm tool /app/yii wasabi-user/delete --username author-giga-d-23-00288
object(Aws\Result)#248 (2) {
["data":"Aws\Result":private]=>
array(1) {
  ["@metadata"]=>
  array(4) {
    ["statusCode"]=>
    int(200)
    ["effectiveUri"]=>
    string(26) "https://iam.wasabisys.com/"
    ["headers"]=>
    array(9) {
      ["content-length"]=>
      string(3) "187"
      ["content-type"]=>
      string(8) "text/xml"
      ["date"]=>
      string(29) "Mon, 08 May 2023 06:16:15 GMT"
      ["server"]=>
      string(9) "WasabiIAM"
      ["vary"]=>
      string(15) "Accept-Encoding"
      ["x-amzn-requestid"]=>
      string(36) "e30add7f-e7de-5547-b74f-1c6d4122992c"
      ["x-wasabi-cluster-id"]=>
      string(9) "us-east-1"
      ["x-wasabi-service"]=>
      string(11) "iam_service"
      ["x-wasabi-service-runtime-id"]=>
      string(12) "8a81f577a439"
    }
    ["transferStats"]=>
    array(1) {
      ["http"]=>
      array(1) {
        [0]=>
        array(0) {
        }
      }
    }
  }
}
["monitoringEvents":"Aws\Result":private]=>
array(0) {
}
}

```

# Run command to create new bucket
```
$ docker-compose run --rm tool /app/yii wasabi-bucket/create --bucketName bucket-giga-d-23-00288
object(Aws\Result)#670 (2) {
  ["data":"Aws\Result":private]=>
  array(2) {
    ["Location"]=>
    string(64) "https://s3.ap-northeast-1.wasabisys.com/bucket-giga-d-23-00288//"
    ["@metadata"]=>
    array(4) {
      ["statusCode"]=>
      int(200)
      ["effectiveUri"]=>
      string(63) "https://s3.ap-northeast-1.wasabisys.com/bucket-giga-d-23-00288/"
      ["headers"]=>
      array(6) {
        ["date"]=>
        string(29) "Mon, 08 May 2023 02:55:18 GMT"
        ["location"]=>
        string(64) "https://s3.ap-northeast-1.wasabisys.com/bucket-giga-d-23-00288//"
        ["server"]=>
        string(48) "WasabiS3/7.13.2207-2023-04-06-dc2f085548 (head2)"
        ["x-amz-id-2"]=>
        string(76) "IpOlaMeki7T9/vzq83JTqKUji/tjk9wA8P2YjFDN/G2DGBfMmu3hpScUt5Q+Qw5Rhljrdn+3zz2C"
        ["x-amz-request-id"]=>
        string(16) "172D4CE79A06051A"
        ["content-length"]=>
        string(1) "0"
      }
      ["transferStats"]=>
      array(1) {
        ["http"]=>
        array(1) {
          [0]=>
          array(0) {
          }
        }
      }
    }
  }
  ["monitoringEvents":"Aws\Result":private]=>
  array(0) {
  }
}
```
# Run command to list buckets
```
$ docker-compose run --rm tool /app/yii wasabi-bucket/list-buckets
object(Aws\Result)#619 (2) {
  ["data":"Aws\Result":private]=>
  array(3) {
    ["Buckets"]=>
    array(2) {
      [0]=>
      array(2) {
        ["Name"]=>
        string(22) "bucket-giga-d-23-00123"
        ["CreationDate"]=>
        object(Aws\Api\DateTimeResult)#665 (3) {
          ["date"]=>
          string(26) "2023-04-18 14:26:01.000000"
          ["timezone_type"]=>
          int(2)
          ["timezone"]=>
          string(1) "Z"
        }
      }
      [1]=>
      array(2) {
        ["Name"]=>
        string(15) "dbgiga-datasets"
        ["CreationDate"]=>
        object(Aws\Api\DateTimeResult)#666 (3) {
          ["date"]=>
          string(26) "2023-04-18 13:59:16.000000"
          ["timezone_type"]=>
          int(2)
          ["timezone"]=>
          string(1) "Z"
        }
      }
    }
    ["Owner"]=>
    array(2) {
      ["DisplayName"]=>
      string(4) "test"
      ["ID"]=>
      string(64) "DB0CFDA9A26F27676CEEF73D523C0824E000A2ADE31598208494A5232972CEBA"
    }
    ["@metadata"]=>
    array(4) {
      ["statusCode"]=>
      int(200)
      ["effectiveUri"]=>
      string(40) "https://s3.ap-northeast-1.wasabisys.com/"
      ["headers"]=>
      array(6) {
        ["content-type"]=>
        string(15) "application/xml"
        ["date"]=>
        string(29) "Mon, 08 May 2023 03:01:56 GMT"
        ["server"]=>
        string(48) "WasabiS3/7.13.2207-2023-04-06-dc2f085548 (head1)"
        ["x-amz-id-2"]=>
        string(76) "i+MDniYmYwh3HK4O59ZXo5EFNSlAt+RYZpWDfOTQUaKV1yyz6WiwiwVuChLN+c07XeXuDZCF2PuM"
        ["x-amz-request-id"]=>
        string(16) "A7847DFC46797C74"
        ["transfer-encoding"]=>
        string(7) "chunked"
      }
      ["transferStats"]=>
      array(1) {
        ["http"]=>
        array(1) {
          [0]=>
          array(0) {
          }
        }
      }
    }
  }
  ["monitoringEvents":"Aws\Result":private]=>
  array(0) {
  }
}
```

# Run command to delete bucket
```
$ docker-compose run --rm tool /app/yii wasabi-bucket/delete --bucketName bucket-giga-d-23-00288
object(Aws\Result)#655 (2) {
  ["data":"Aws\Result":private]=>
  array(1) {
    ["@metadata"]=>
    array(4) {
      ["statusCode"]=>
      int(204)
      ["effectiveUri"]=>
      string(63) "https://s3.ap-northeast-1.wasabisys.com/bucket-giga-d-23-00288/"
      ["headers"]=>
      array(4) {
        ["date"]=>
        string(29) "Mon, 08 May 2023 03:08:08 GMT"
        ["server"]=>
        string(48) "WasabiS3/7.13.2207-2023-04-06-dc2f085548 (head1)"
        ["x-amz-id-2"]=>
        string(76) "kZoKr7y9pCYNPYZc9bAXkclMxW4AZsEkeatw7uOuMpyiE9PLgtc6AIVb3YJj2yEGjGNHOymQylBF"
        ["x-amz-request-id"]=>
        string(16) "C4326CB18941F63B"
      }
      ["transferStats"]=>
      array(1) {
        ["http"]=>
        array(1) {
          [0]=>
          array(0) {
          }
        }
      }
    }
  }
  ["monitoringEvents":"Aws\Result":private]=>
  array(0) {
  }
}
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
