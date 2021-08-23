# AWS permissions policy for ec2

Allows instances to be launched but users can only start, stop and terminate
instances they own.

Users are restricted to using EC2 instances in Hong Kong region and can only
launch t3.nano and t3.micro instance types. Instances must have an Owner tag 
with a value that is your AWS username. Also, a Name tag is required. This
value could have a syntax such `ec2-ape1-staging-gigadb` where:
* ec2     => AWS resource
* ape1    => Hong Kong ap-east-1 region of the EC2 instance
* staging => environment
* gigadb  => deployed application name

A single Security Token Service permission is included to allow decoding of 
encoded messages which are displayed when users encounter permission errors 
when using the AWS management console.

## Policy Name: `GigadbEC2Access`
```
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "NonResourceBasedPermissions",
            "Effect": "Allow",
            "Action": [
                "ec2:Describe*",
                "ec2:AssociateAddress",
                "ec2:DisassociateAddress",
                "ec2:ImportKeyPair",
                "ec2:CreateKeyPair",
                "ec2:CreateSecurityGroup",
                "ec2:CreateTags",
                "sts:DecodeAuthorizationMessage"
            ],
            "Resource": "*"
        },
        {
            "Sid": "SecurityGroupActions",
            "Effect": "Allow",
            "Action": [
                "ec2:AuthorizeSecurityGroupIngress",
                "ec2:DeleteSecurityGroup",
                "ec2:RevokeSecurityGroupEgress",
                "ec2:AuthorizeSecurityGroupEgress"
            ],
            "Resource": "*"
        },
        {
            "Sid": "RunInstancesWithRegionAndInstanceTypeRestriction",
            "Effect": "Allow",
            "Action": "ec2:RunInstances",
            "Resource": "*",
            "Condition": {
                "StringEquals": {
                    "ec2:Region": "ap-east-1"
                },
                "ForAllValues:StringLike": {
                    "ec2:InstanceType": [
                        "t3.nano",
                        "t3.micro"
                    ]
                }
            }
        },
        {
            "Sid": "RunInstancesWithOwnerTagRestriction",
            "Effect": "Deny",
            "Action": [
                "ec2:CreateVolume",
                "ec2:RunInstances"
            ],
            "Resource": [
                "arn:aws:ec2:*:*:volume/*",
                "arn:aws:ec2:*:*:instance/*"
            ],
            "Condition": {
                "StringNotLike": {
                    "aws:RequestTag/Owner": "${aws.username}"
                }
            }
        },
        {
            "Sid": "RunInstancesWithNameTagRestriction",
            "Effect": "Deny",
            "Action": [
                "ec2:CreateVolume",
                "ec2:RunInstances"
            ],
            "Resource": [
                "arn:aws:ec2:*:*:volume/*",
                "arn:aws:ec2:*:*:instance/*"
            ],
            "Condition": {
                "StringNotLike": {
                    "aws:RequestTag/Name": "*"
                }
            }
        },
        {
            "Sid": "AllowCreateTagsOnRunInstance",
            "Effect": "Allow",
            "Action": "ec2:CreateTags",
            "Resource": "*",
            "Condition": {
                "StringEquals": {
                    "ec2:CreateAction": "RunInstances"
                }
            }
        },
        {
            "Sid": "ModifyInstancesWithOwnerTagRestriction",
            "Effect": "Allow",
            "Action": [
                "ec2:TerminateInstances",
                "ec2:StartInstances",
                "ec2:StopInstances"
            ],
            "Resource": "*",
            "Condition": {
                "StringEquals": {
                    "ec2:ResourceTag/Owner": "${aws:username}"
                }
            }
        }
    ]
}
```