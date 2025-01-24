# AWS 

This page contains custom policies for AWS IAM users. Each developer would 
have a `user/$name` user created, and the following policies would be attached to
the user via the group `group/$group` in the AWS IAM Dashboard, the available
groups are `group/developers` `group/Applications` and `group/administrators`

## Currently in effect custom policies

- [EC2](policy-ec2.json)
- [EFS](policy-efs.json)
- [RDS](policy-rds.json)
- [s3](policy-s3.json)
- [VPC](policy-vpc.json)
- [s3-gigadb-dataset-files](policy-s3-gigadb-dataset-files.json)
- [db-ec2-vpc-actions](policy-db-ec2-vpc-actions.json)


#### policy-ec2.json: AWS permissions policy for ec2

Allows instances to be launched but users can only start, stop and terminate
their own instances.

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

Policy Name: GigadbEC2Access

#### policy-rds.json:  AWS permissions policy for RDS

Policy Name: GigadbRDSAccess

#### policy-vpc.json: Policy for managing VPCs

In order to allow Terraform to manage the Network ACLs for VPC, the following policy is necessary


## AWS Command Line Interface

The AWS command line interface (CLI) is required for:
* allowing terraform configurations to retrieve your IAM username for appropriately tagging resources created on AWS
* decoding error messages that are provided by the AWS management console when there is a problem with performing an operation.



#### Installation

Information to install AWS CLI is available
[here](https://docs.aws.amazon.com/cli/latest/userguide/cli-chap-install.html).

#### Configuration

AWS CLI requires configuration to use your AWS account:

`~/.aws/config`
```
[default]
region=ap-east-1
output=json

[profile gigadb]
region=ap-east-1
output=json
```

`~/.aws/credentials`
```
[gigadb]
aws_access_key_id=ASDSFSDFFEXAMPLE
aws_secret_access_key=eDFDSG$dgFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY
```

To test your configuration:
```
$ aws configure list --profile gigadb
      Name                    Value             Type    Location
      ----                    -----             ----    --------
   profile                    Username           manual    --profile
access_key     ****************YYGK shared-credentials-file    
secret_key     ****************EVDq shared-credentials-file    
    region                ap-east-1      config-file    ~/.aws/config
```

>**Note:** Core team member may want to have an additional profile called `upstream` to store the access keys used for deploying the Upstream pipeline

#### Decoding error messages

If an error message is displayed on your AWS management console, it can be
decoded as follows:
```
$ aws sts --profile gigadb decode-authorization-message --encoded-message (encoded error message) --query DecodedMessage --output text | jq '.'

```
