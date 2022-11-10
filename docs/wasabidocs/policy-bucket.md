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
        "s3:PutObjectAcl",
        "s3:DeleteObject"
      ],
      "Resource": [
        "arn:aws:s3:::test-gigadb-datasets",
        "arn:aws:s3:::test-gigadb-datasets/*",
        "arn:aws:s3:::gigadb-datasets",
        "arn:aws:s3:::gigadb-datasets/*"
      ]
    },
    {
      "Sid": "NotAllowDevelopersToDeleteGigaDbDatasetsCBucket",
      "Effect": "Deny",
      "Action": "s3:DeleteBucket",
      "Resource": [
        "arn:aws:s3:::test-gigadb-datasets",
        "arn:aws:s3:::gigadb-datasets"
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
      "Action": ["s3:ListAllMyBuckets", "s3:GetBucketLocation"],
      "Effect": "Allow",
      "Resource": ["arn:aws:s3:::*"]
    },
    {
        "Sid": "AllowSystemsUserToListGigadbDatasetBucketStagingFolder",
        "Action": ["s3:ListBucket"],
        "Effect": "Allow",
        "Resource": ["arn:aws:s3:::gigadb-datasets"],
        "Condition": {"StringEquals": {"s3:prefix": ["", "staging/"],"s3:delimiter":["/"]}}
    },
    {
        "Sid": "AllowSystemsUserToListGigadbDatasetBucketLiveFolder",
        "Action": ["s3:ListBucket"],
        "Effect": "Allow",
        "Resource": ["arn:aws:s3:::gigadb-datasets"],
        "Condition": {"StringEquals": {"s3:prefix": ["","live/"],"s3:delimiter":["/"]}}
    },
    {
         "Sid": "AllowSystemsUserToListgStagingFolder",
         "Action": ["s3:ListBucket"],
         "Effect": "Allow",
         "Resource": ["arn:aws:s3:::gigadb-datasets"],
         "Condition":{"StringLike":{"s3:prefix":["gigadb-datasets/staging/*"]}}
    },
    {
         "Sid": "AllowSystemsUserToListLiveFolder",
         "Action": ["s3:ListBucket"],
         "Effect": "Allow",
         "Resource": ["arn:aws:s3:::gigadb-datasets"],
         "Condition":{"StringLike":{"s3:prefix":["gigadb-datasets/live/*"]}}
    },
    {
	  "Sid": "AllowSystemUsersToPutIntoStagingAndLiveFolders",
      "Effect": "Allow",
      "Action": [
				"s3:PutObject",
				"s3:PutObjectAcl"
			],
      "Resource": [
        "arn:aws:s3:::gigadb-datasets/staging/*",
		"arn:aws:s3:::gigadb-datasets/live/*"
      ]
    },
	{
      "Sid": "NotAllowSystemUsersToDeleteStagingAndLiveGigadbDatasetsBucket",
      "Effect": "Deny",
      "Action": "s3:DeleteBucket",
      "Resource": [
        "arn:aws:s3:::gigadb-datasets/staging",
		"arn:aws:s3:::gigadb-datasets/live"
      ]
    }
  ]
}
```