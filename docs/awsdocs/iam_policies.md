# IAM custom policies

## policy-ec2.json: AWS permissions policy for ec2

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

Policy Name: GigadbEC2Access

## policy-rds.json:  AWS permissions policy for RDS

Policy Name: GigadbRDSAccess

## policy-vpc.json: Policy for managing VPCs

In order to allow Terraform to manage the Network ACLs for VPC, the following policy is necessary


