# Wasabi User Drop Boxes

Wasabi provides some [documentation](https://wasabi-support.zendesk.com/hc/en-us/articles/360000016712-How-do-I-set-up-Wasabi-for-user-access-separation-)
for how to manage submitter users and their dataset file uploads into separate 
buckets or separate directories within a bucket.

## Separate buckets for each user

In this scenario, each submitter has access to their own bucket but not any 
other buckets:
* user1 has access to `gigadb-bucket-user1` & user2 has access to
  `gigadb-bucket-user2`
* user1 has no access to `gigadb-bucket-user2` and user2 has no access to 
  `gigadb-bucket-user1`

>It is not a good idea to name a bucket `user1`, `user2`, etc since it is likely
>that these bucket names already exist in an availability region and so cannot 
>be used by us.

* Create a bucket called: `gigadb-bucket-user1`
* Create a dynamic IAM policy for user1 called `AllowUsersReadWriteDenyDeleteOwnBucket`:
```
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": "s3:ListAllMyBuckets",
      "Resource": "arn:aws:s3:::*"
    },
    {
      "Effect": "Allow",
      "Action": "s3:*",
      "Resource": [
        "arn:aws:s3:::gigadb-bucket-${aws:username}",
        "arn:aws:s3:::gigadb-bucket-${aws:username}/*"
      ]
    },
    {
      "Effect": "Deny",
      "Action": "s3:DeleteBucket",
      "Resource": [
        "arn:aws:s3:::gigadb-bucket-${aws:username}",
        "arn:aws:s3:::gigadb-bucket-${aws:username}/*"
      ]
    }
  ]
}
```

>To be able to use the Wasabi Explorer, the sub-user must have `ListAllMyBuckets` 
>permission. This will allow the sub-user to list all the buckets when logged 
>into the console, but can only access and see contents from the bucket that 
>sub-user has permission to.


For extra safety, we can have an extra explicit deny policy called 
`DenyUsersOtherBuckets` to stop users from accessing other buckets:
```
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Deny",
      "Action": [
        "s3:ListBucket",
        "s3:GetBucketLocation",
        "s3:ListBucketMultipartUploads"
      ],
      "NotResource": [
        "arn:aws:s3:::gigadb-bucket-${aws:username}/*",
        "arn:aws:s3:::gigadb-bucket-${aws:username}"
      ]
    },
    {
      "Effect": "Deny",
      "Action": "s3:*",
      "NotResource": [
        "arn:aws:s3:::gigadb-bucket-${aws:username}/*",
        "arn:aws:s3:::gigadb-bucket-${aws:username}"
      ]
    }
  ]
}
```

Now, create a new user account called `user1`. Select the Programmatic (create 
API key) option so an API key set is created for future use with this user's 
storage app. Also provide `user1` with console access if needed.

## Separate directories for each user within one bucket

In this scenario, each user has their own folder that is not accessible by other 
users with access to the same bucket). Therefore, separation at the folder level
involves:

* user1 and user2 sharing a common bucket
* user1 has access to folder1 whilst user2 has access to folder2
* user1 has no access to folder2 and user2 has no access to folder1

Policy to be attached to the sub-user to give access to a specific folder within
a bucket called `gigadb-upload` that is specifically used for dataset uploads
from submitters:
```
{
  "Version":"2012-10-17",
  "Statement": [
    {
      "Sid": "AllowUserToSeeBucketListInTheConsole",
      "Action": ["s3:ListAllMyBuckets", "s3:GetBucketLocation", "s3:GetBucketCompliance"],
      "Effect": "Allow",
      "Resource": ["arn:aws:s3:::*"]
    },
    {
      "Sid": "AllowRootAndHomeListingOfCompanyBucket",
      "Action": ["s3:ListBucket"],
      "Effect": "Allow",
      "Resource": ["arn:aws:s3:::gigadb-upload"],
      "Condition":{"StringEquals":{"s3:prefix":["","${aws:username}/"],"s3:delimiter":["/"]}}
    },
    {
      "Sid": "AllowListingOfUserFolder",
      "Action": ["s3:ListBucket"],
      "Effect": "Allow",
      "Resource": ["arn:aws:s3:::gigadb-upload"],
      "Condition":{"StringLike":{"s3:prefix":["${aws:username}/*"]}}
    },
    {
      "Sid": "AllowAllS3ActionsInUserFolder",
      "Effect": "Allow",
      "Action": ["s3:*"],
      "Resource": ["arn:aws:s3:::gigadb-upload/${aws:username}/*"]
    }
  ]
}
```

## Transfer files to Wasabi bucket using Wasabi Explorer

[Wasabi Explorer](https://wasabi.com/help/downloads/) provides a desktop app 
for users to upload files. The user needs to add their Wasabi user account into
Wasabi Explorer using the credentials (access key and secret key) that we will
provide them with. Wasabi Explorer can then be used to transfer files into their
bucket or directory using dropdown menus.

## Automate infrastructure for private user drop boxes

The setup of the 100 plus user drop boxes can be [automated](https://wasabi-support.zendesk.com/hc/en-us/articles/360057225472).