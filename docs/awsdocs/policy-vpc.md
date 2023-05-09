# Policy for managing VPCs

In order to allow Terraform to manage the Network ACLs for VPC, the following policy is necessary

```
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "GigaDbEC2VpcNonresourceSpecificActions",
            "Effect": "Allow",
            "Action": [
                "ec2:CreateNetworkAclEntry",
                "ec2:DeleteNetworkAcl",
                "ec2:DeleteNetworkAclEntry",
                "ec2:DeleteRoute",
                "ec2:DeleteRouteTable",
                "ec2:AuthorizeSecurityGroupEgress",
                "ec2:AuthorizeSecurityGroupIngress",
                "ec2:RevokeSecurityGroupEgress",
                "ec2:RevokeSecurityGroupIngress",
                "ec2:DeleteSecurityGroup"
            ],
            "Resource": "*",
            "Condition": {
                "StringEquals": {
                    "ec2:Region": [
                        "ap-east-1",
                        "ap-northeast-1",
                        "ap-northeast-2",
                        "ap-southeast-1",
                        "eu-west-3"
                    ]
                }
            }
        }
    ]
}

```