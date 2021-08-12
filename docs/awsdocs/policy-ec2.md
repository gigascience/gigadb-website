# AWS permissions policy for ec2

Launch instances but users can only start, stop and terminate instances they 
own. Also, users are restricted to using EC2 in Hong Kong region and can only
launch t3.nano and t3.micro instance types.

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
            "Sid": "VisualEditor1",
            "Effect": "Allow",
            "Action": "ec2:RunInstances",
            "Resource": "*",
            "Condition": {
                "StringEquals": {
                    "ec2:Region": "ap-east-1"
                },
                "StringLike": {
                    "ec2:InstanceType": "t3.micro"
                }
            }
        },
        {
            "Sid": "RunInstancesWithTagRestrictions",
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
            "Sid": "VisualEditor2",
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