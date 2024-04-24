```
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "AllowS3ReadWrite",
            "Effect": "Allow",
            "Action": [
                "s3:ListBucket",
                "s3:GetObject",
                "s3:GetObjectAcl",
                "s3:PutObject",
                "s3:PutObjectAcl",
                "s3:ReplicateObject",
                "s3:DeleteObject"
            ],
            "Resource": [
                "arn:aws:s3:::assets.gigadb-cdn.net",
                "arn:aws:s3:::assets.gigadb-cdn.net/*",
                "arn:aws:s3:::gigadb-database-backups",
                "arn:aws:s3:::gigadb-database-backups/*"
            ]
        }
    ]
}
```

## S3 policy: AllowReadWriteBucketGigadbDatasetsMetadata

This policy allows IAM users to read and write to gigadb-datasets-metadata
bucket which stores $doi.md5 and $doi.filesizes files.

```
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "AllowS3ReadWriteBucketGigadbDatasetsMetadata",
            "Effect": "Allow",
            "Action": [
                "s3:ListBucket",
                "s3:GetObject",
                "s3:GetObjectAcl",
                "s3:PutObject",
                "s3:PutObjectAcl",
                "s3:ReplicateObject",
                "s3:DeleteObject",
                "s3:GetBucketLocation"
            ],
            "Resource": [
                "arn:aws:s3:::gigadb-datasets-metadata",
                "arn:aws:s3:::gigadb-datasets-metadata/*"
            ]
        }
    ]
}
```