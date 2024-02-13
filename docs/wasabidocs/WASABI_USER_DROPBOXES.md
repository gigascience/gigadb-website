# Wasabi User Drop Boxes

Wasabi provides some [documentation](https://wasabi-support.zendesk.com/hc/en-us/articles/360000016712-How-do-I-set-up-Wasabi-for-user-access-separation-)
for how to manage authors and their dataset file uploads into separate 
buckets.

## Separate buckets for each user

In this scenario, each author has a Wasabi user account which is able to access 
their own bucket but not any other buckets:
* User `giga-d-23-00123` has access to the `bucket-giga-d-23-00123` bucket and 
  user `giga-d-23-00288` has access to the `bucket-giga-d-23-00288` bucket.
* User `giga-d-23-00123` has no access to the `bucket-giga-d-23-00288` bucket 
  and user `giga-d-23-00288` has no access to `bucket-giga-d-23-00123`.

Bucket names are not allowed to contain uppercase letters so cannot be named
`bucket-GIGA-D-23-00123` and instead must be `giga-d-23-00123`. Since we are 
using dynamic policies for bucket security involving the interpolation of 
`${aws:username}`, authors are required to have username `giga-d-23-00123`.

* Create a bucket called: `bucket-giga-d-23-00123`
* Create a dynamic IAM policy called `AllowReadWriteOnOwnBucket` to be applied
  the `Curators` group:
```
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": "s3:*",
      "Resource": [
        "arn:aws:s3:::bucket-${aws:username}",
        "arn:aws:s3:::bucket-${aws:username}/*"
      ]
    },
  ]
}
```
Create a dynamic IAM policy for user1 called `DenyDeleteOwnBucket` to be applied
to the `Curators` group::
```
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Deny",
      "Action": "s3:DeleteBucket",
      "Resource": [
        "arn:aws:s3:::bucket-${aws:username}",
        "arn:aws:s3:::bucket-${aws:username}/*"
      ]
    }
  ]
}
```

To be able to use the Wasabi Explorer and other tools, the sub-user must have 
`ListAllMyBuckets` permission. This will allow the sub-user to list all the 
buckets when logged into the console, but can only access and see contents from 
the bucket that sub-user has permission to.

For extra safety, we can have an extra explicit deny policy called 
`DenyAccessToOtherBuckets` to stop users from accessing other buckets:
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
        "arn:aws:s3:::bucket-${aws:username}/*",
        "arn:aws:s3:::bucket-${aws:username}"
      ]
    },
    {
      "Effect": "Deny",
      "Action": "s3:*",
      "NotResource": [
        "arn:aws:s3:::bucket-${aws:username}/*",
        "arn:aws:s3:::bucket-${aws:username}"
      ]
    }
  ]
}
```

Now, create a new user account called `giga-d-23-00123`. Select the Programmatic
(create API key) option so an API key set is created for future use with this 
user's storage app. Also provide `giga-d-23-00123` user with console access if 
needed.

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

## Copy files from author bucket to live directory in gigadb-datasets bucket

Rclone can be used to transfer files from an author's drop box to the live
storage area in the `gigadb-datasets` bucket.

### Install Rclone

See https://rclone.org/install/ for instructions to install rclone command line
tool.

### Create rclone configuration to access Wasabi buckets

You should already have a Wasabi access key and secret access key for your user
account in Wasabi. With the [config](https://rclone.org/commands/rclone_config/)
tool in rclone to create a configuration to access the GigaDB buckets in Wasabi:
```
$ rclone config
```

You will be asked to provide answers to a series of questions which will result
in a configuration similar to this:
```
[wasabi]
type = s3
provider = Wasabi
access_key_id = A23KJH9DUMMYKJHKAAAAA
secret_access_key = dlakgalkkgj344DUMMYdfakjaklg
endpoint = s3.ap-northeast-1.wasabisys.com
acl = public-read
```

### Copy procedure

Using the Wasabi web console, create a new directory named by the DOI number for
the dataset in the live section of the `gigadb-datasets` bucket.

> Currently, it is [not possible](https://forum.rclone.org/t/on-s3-rclone-should-create-persistent-empty-folders/16228/2)
> to create the new directory in Wasabi using rclone.

Use [rclone copy](https://rclone.org/commands/rclone_copy/) to copy author's 
dataset files into the newly created directory. For example:
```
$ rclone copy wasabi:bucket-giga-d-23-00123/ wasabi:gigadb-datasets/live/pub/10.5524/102001_103000/102304/
```

> Where `102304` is the new DOI directory for the dataset.