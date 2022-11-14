# Wasabi permissions policy for bucket


### General policy
The policy `IAMUsersManagePasswordAndKeys` is attached to the group `Developers` and `Systems`, which allows all developers and systems user to manage their own password and access keys in the Wasabi console.

Policy: IAMUsersManagePasswordAndKeys
```
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "AllowManageOwnPasswords",
      "Effect": "Allow",
      "Action": [
        "iam:ChangePassword",
        "iam:GetUser"
      ],
      "Resource": "arn:aws:iam::*:user/${aws:username}"
    },
    {
      "Sid": "AllowManageOwnAccessKeys",
      "Effect": "Allow",
      "Action": [
        "iam:CreateAccessKey",
        "iam:DeleteAccessKey",
        "iam:ListAccessKeys",
        "iam:UpdateAccessKey"
      ],
      "Resource": "arn:aws:iam::*:user/${aws:username}"
    }
  ]
}
```

### Group `Developers` policy

The policy `AllowDevelopersToSeeBucketListInTheConsole` is attached to the group `Developers`, which allows all developers to list every bucket in the Wasabi console.
It also allows developers to access all buckets, but is not allowed to delete buckets `gigadb-datasets` and `test-gigadb-datasets`.


Policy: AllowDevelopersToListGetPutInGigadbDatasetsBucket
```
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "AllowDevelopersToSeeBucketListInTheConsole",
      "Effect": "Allow",
      "Action": [
        "s3:ListAllMyBuckets",
        "s3:GetBucketVersioning"
      ],
      "Resource": "arn:aws:s3:::*"
    },
    {
      "Sid": "AllowAccessToGigaDbDatasetsBucket",
      "Effect": "Allow",
      "Action": [
        "s3:ListBucket",
        "s3:GetObject",
        "s3:PutObject",
        "s3:GetObjectAcl",
        "s3:PutObjectAcl"
      ],
      "Resource": [
        "arn:aws:s3:::gigadb-datasets",
        "arn:aws:s3:::gigadb-datasets/*"
      ]
    },
	{
	  "Sid": "AllowAllS3ActionInTestGigaDbDatasetsBucket",
      "Effect": "Allow",
      "Action": [
        "s3:*"
      ],
      "Resource": [
        "arn:aws:s3:::test-gigadb-datasets",
        "arn:aws:s3:::test-gigadb-datasets/*"
      ]
	},
    {
      "Sid": "NotAllowDevelopersToDeleteGigaDbDatasetsCBucket",
      "Effect": "Deny",
      "Action": ["s3:DeleteBucket","s3:DeleteObject"],
      "Resource": [
        "arn:aws:s3:::test-gigadb-datasets",
        "arn:aws:s3:::gigadb-datasets/staging",
        "arn:aws:s3:::gigadb-datasets/staging/*",
        "arn:aws:s3:::gigadb-datasets/live",
        "arn:aws:s3:::gigadb-datasets/live/*"
      ]
    }
  ]
}
```

### Group `Systems` policy

The policy `AllowSystemUsersToListAndPutStagingAndLiveGigadbDatasetsBucket` is attached to the group `Systems`, which only allows `Systems` to list and put `gigadb-datasets/staging` and `gigadb-datasets/staging` bucket in the Wasabi console.


Policy:AllowSystemUsersToListAndPutStagingAndLiveGigadbDatasetsBucket
```
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "AllowSystemsUserToSeeBucketListInTheConsole",
      "Effect": "Allow",
      "Action": [
        "s3:ListAllMyBuckets",
        "s3:GetBucketLocation"
      ],
      "Resource": "arn:aws:s3:::*"
    },
    {
      "Sid": "AllowSystemsUserToListGigadbDatasetsBucket",
      "Effect": "Allow",
      "Action": "s3:ListBucket",
      "Resource": [
        "arn:aws:s3:::gigadb-datasets",
        "arn:aws:s3:::gigadb-datasets/*"
      ]
    },
    {
      "Sid": "AllowSystemsUserToListAndPutGigadbDatasetBucketStagingFolder",
      "Effect": "Allow",
      "Action": [
        "s3:GetObject",
        "s3:GetObjectVersion",
        "s3:ListBucket",
        "s3:PutObject",
        "s3:PutObjectAcl"
      ],
      "Resource": [
        "arn:aws:s3:::gigadb-datasets/staging",
        "arn:aws:s3:::gigadb-datasets/staging/*"
      ]
    },
    {
      "Sid": "AllowSystemsUserToListAndPutGigadbDatasetBucketLiveFolder",
      "Effect": "Allow",
      "Action": [
        "s3:GetObject",
        "s3:GetObjectVersion",
        "s3:ListBucket",
        "s3:PutObject",
        "s3:PutObjectAcl"
      ],
      "Resource": [
        "arn:aws:s3:::gigadb-datasets/live",
        "arn:aws:s3:::gigadb-datasets/live/*"
      ]
    },
    {
      "Sid": "NotAllowSystemUsersToDeleteGigadbDatasetsBucketAndObject",
      "Effect": "Deny",
      "Action": [
        "s3:DeleteBucket",
        "s3:DeleteObject"
      ],
      "Resource": [
        "arn:aws:s3:::gigadb-datasets",
        "arn:aws:s3:::gigadb-datasets/*"
      ]
    }
  ]
}
```