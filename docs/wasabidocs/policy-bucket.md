# Wasabi permissions policy for bucket


### Group `Developers` policy

The policy `AllowDevelopersToSeeBucketListInTheConsole` is attached to the group `Developers`, which allows all developers to list every bucket in the Wasabi console. 

Policy: AllowDevelopersToSeeBucketListInTheConsole
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
    }
  ]
}
```

The policy `IAMUsersManagePasswordAndKeys` is  attached to the group `Developers`, which allows all developers to manage their own password and access keys in the Wasabi console.

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

### User policy
This policy is attached to each user, which allows user to access bucket `gigadb-cngb-backup` only, and developer is not allowed to delete bucket `gigadb-cngb-backup`. 

Policy Name: AllowS3ReadWrite
```
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "AllowAccessToGigaDbCngbBackUpBucket",
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
        "arn:aws:s3:::gigadb-cngb-backup",
        "arn:aws:s3:::gigadb-cngb-backup/*"
      ]
    },
    {
      "Sid": "NotAllowDevelopersToDeleteGigaDbCngbBackUpBucket",
      "Effect": "Deny",
      "Action": "s3:DeleteBucket",
      "Resource": "arn:aws:s3:::gigadb-cngb-backup"
    }
  ]
}
```