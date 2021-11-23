# AWS permissions policy for RDS

Policy Name: GigadbRDSAccess
```
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "AllowRDSListDescribe",
            "Effect": "Allow",
            "Action": [
                "rds:Describe*",
                "rds:List*"
            ],
            "Resource": "*"
        },
        {
            "Sid": "AllowEC2Describe",
            "Effect": "Allow",
            "Action": [
                "ec2:Describe*",
                "ec2:DescribeSubnets"
            ],
            "Resource": "*"
        },
        {
            "Sid": "AllowIAMListDescribe",
            "Effect": "Allow",
            "Action": [
                "iam:ListAttachedRolePolicies",
                "iam:GetRole",
                "iam:ListInstanceProfilesForRole"
            ],
            "Resource": "*"
        },
        {
            "Sid": "WorkWithElasticIPAddresses",
            "Effect": "Allow",
            "Action": [
                "ec2:DescribeAddresses",
                "ec2:AllocateAddress",
                "ec2:DescribeInstances",
                "ec2:AssociateAddress",
                "ec2:DescribeNetworkInterfaces"
            ],
            "Resource": "*"
        },
        {
            "Sid": "ModifyElasticIPAddresses",
            "Effect": "Allow",
            "Action": [
                "ec2:DisassociateAddress",
                "ec2:ReleaseAddress"
            ],
            "Resource": "*",
            "Condition": {
                "StringEqualsIgnoreCase": {
                    "ec2:ResourceTag/Owner": "${aws:username}"
                }
            }
        },
        {
            "Sid": "CreateRDSInstance",
            "Effect": "Allow",
            "Action": [
                "iam:CreateRole",
                "iam:TagRole",
                "iam:AttachRolePolicy",
                "ec2:CreateInternetGateway",
                "ec2:AttachInternetGateway",
                "ec2:AssociateVpcCidrBlock",
                "ec2:CreateRoute",
                "ec2:CreateRouteTable",
                "ec2:AssociateRouteTable",
                "ec2:CreateSubnet",
                "ec2:CreateDefaultSubnet",
                "ec2:ModifySubnetAttribute",
                "ec2:CreateSecurityGroup",
                "ec2:CreateVpc",
                "ec2:ModifyVpcAttribute",
                "ec2:GetManagedPrefixListEntries",
                "ec2:AssociateSubnetCidrBlock",
                "rds:CreateDBSubnetGroup",
                "rds:AddTagsToResource",
                "ec2:GetManagedPrefixListAssociations",
                "ec2:CreateNatGateway"
            ],
            "Resource": "*"
        },
        {
            "Sid": "CreateRDSInstancesWithRegionAndInstanceTypeRestriction",
            "Effect": "Allow",
            "Action": "rds:CreateDBInstance",
            "Resource": "*",
            "Condition": {
                "StringEquals": {
                    "rds:DatabaseEngine": "postgres",
                    "rds:DatabaseClass": "db.t3.micro",
                    "aws:RequestedRegion": "ap-east-1"
                }
            }
        },
        {
            "Sid": "CreateRDSInstancesWithOwnerTagRestriction",
            "Effect": "Allow",
            "Action": "rds:CreateDBInstance",
            "Resource": "*",
            "Condition": {
                "StringEqualsIgnoreCase": {
                    "aws:RequestTag/Owner": "${aws:username}"
                }
            }
        },
        {
            "Sid": "RestoreDBInstanceToPointInTime",
            "Effect": "Allow",
            "Action": [
                "rds:RestoreDBInstanceToPointInTime",
                "rds:DeleteDBInstanceAutomatedBackup"
            ],
            "Resource": "*"
        },
        {
            "Sid": "DeleteEC2ResourcesWithOwnerTagRestriction",
            "Effect": "Allow",
            "Action": [
                "iam:DeleteRole",
                "ec2:DeleteSubnet",
                "ec2:DeleteLocalGatewayRouteTableVpcAssociation",
                "ec2:UpdateSecurityGroupRuleDescriptionsIngress",
                "ec2:DeleteRouteTable",
                "ec2:RevokeSecurityGroupEgress",
                "ec2:UnassignIpv6Addresses",
                "ec2:DeleteInternetGateway",
                "ec2:UnassignPrivateIpAddresses",
                "ec2:UpdateSecurityGroupRuleDescriptionsEgress",
                "ec2:DetachInternetGateway",
                "ec2:DisassociateRouteTable",
                "ec2:RevokeSecurityGroupIngress",
                "ec2:DeleteVpc",
                "ec2:DeleteRoute",
                "ec2:DisassociateRouteTable",
                "ec2:DeleteNatGateway"
            ],
            "Resource": "*",
            "Condition": {
                "StringEqualsIgnoreCase": {
                    "ec2:ResourceTag/Owner": "${aws:username}"
                }
            }
        },
        {
            "Sid": "DeleteDBSubnetWithOwnerTagRestriction",
            "Action": [
                "rds:ModifyDBSubnetGroup",
                "rds:DeleteDBSubnetGroup",
                "ram:GetResourceShareAssociations"
            ],
            "Effect": "Allow",
            "Resource": "*",
            "Condition": {
                "StringEqualsIgnoreCase": {
                    "rds:subgrp-tag/Owner": "${aws:username}"
                }
            }
        },
        {
            "Sid": "ManageDBInstancesWithOwnerTagRestriction",
            "Action": [
                "rds:DeleteDBInstance",
                "rds:RebootDBInstance",
                "rds:ModifyDBInstance",
                "rds:CreateDBSnapshot"
            ],
            "Effect": "Allow",
            "Resource": "*",
            "Condition": {
                "StringEqualsIgnoreCase": {
                    "rds:db-tag/Owner": "${aws:username}"
                }
            }
        },
        {
            "Sid": "ManageOptionGroupsWithOwnerTagRestriction",
            "Action": [
                "rds:ModifyOptionGroup",
                "rds:DeleteOptionGroup"
            ],
            "Effect": "Allow",
            "Resource": "*",
            "Condition": {
                "StringEqualsIgnoreCase": {
                    "rds:og-tag/Owner": "${aws:username}"
                }
            }
        },
        {
            "Sid": "ManageDBParameterGroupWithOwnerTagRestriction",
            "Action": [
                "rds:ModifyDBParameterGroup",
                "rds:ResetDBParameterGroup"
            ],
            "Effect": "Allow",
            "Resource": "*",
            "Condition": {
                "StringEqualsIgnoreCase": {
                    "rds:pg-tag/Owner": "${aws:username}"
                }
            }
        },
        {
            "Sid": "ManageDBSecurityGroupWithOwnerTagRestriction",
            "Action": [
                "rds:AuthorizeDBSecurityGroupIngress",
                "rds:RevokeDBSecurityGroupIngress",
                "rds:DeleteDBSecurityGroup"
            ],
            "Effect": "Allow",
            "Resource": "*",
            "Condition": {
                "StringEqualsIgnoreCase": {
                    "rds:secgrp-tag/Owner": "${aws:username}"
                }
            }
        },
        {
            "Sid": "ManageDBSnapshotWithOwnerTagRestriction",
            "Action": [
                "rds:DeleteDBSnapshot",
                "rds:RestoreDBInstanceFromDBSnapshot"
            ],
            "Effect": "Allow",
            "Resource": "*",
            "Condition": {
                "StringEqualsIgnoreCase": {
                    "rds:snapshot-tag/Owner": "${aws:username}"
                }
            }
        },
        {
            "Sid": "ManageEventSubscriptionsWithOwnerTagRestriction",
            "Action": [
                "rds:ModifyEventSubscription",
                "rds:AddSourceIdentifierToSubscription",
                "rds:RemoveSourceIdentifierFromSubscription",
                "rds:DeleteEventSubscription"
            ],
            "Effect": "Allow",
            "Resource": "*",
            "Condition": {
                "StringEqualsIgnoreCase": {
                    "rds:es-tag/Owner": "${aws:username}"
                }
            }
        }
    ]
}
```