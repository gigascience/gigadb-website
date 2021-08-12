# AWS permissions policy for ec2

Launch instances but users can only start, stop and terminate instances they 
own. Users are restricted to using EC2 in Hong Kong region and can only
launch t3.nano and t3.micro instance types. Instances must have an Owner tag 
with a value that is your AWS username. Also, a Name tag is required. This
value could have a syntax such `ec2-ap-east-1-staging-gigadb` where:
* ec2 is the AWS resource
* ap-east-1 is the region that the EC2 instance is deployed in
* staging is the environment
* gigadb is the name of the deployed application

```
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "VisualEditor0",
            "Effect": "Allow",
            "Action": [
                "ec2:Describe*",
                "ec2:AuthorizeSecurityGroupIngress",
                "ec2:CreateKeyPair",
                "ec2:CreateSecurityGroup"
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