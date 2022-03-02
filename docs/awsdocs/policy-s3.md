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
                "arn:aws:s3:::assets.gigadb-cdn.net/*"
            ]
        }
    ]
}
```