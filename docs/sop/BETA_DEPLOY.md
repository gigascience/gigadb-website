# SOP: Deploying to beta.gigadb.org

A deployment of the GigaDB website code in the `Upstream` Gitlab group to the
`live` environment provides the website that is located at beta.gigadb.org.

## Check domain name resolution

The beta.gigadb.org domain name has been allocated with an AWS elastic IP 
address. You can check the current elastic IP address pointing to 
beta.gigadb.org from the AWS EC2 console:

1. Go to the [cnhk-infra CI/CD variables page](https://gitlab.com/gigascience/cnhk-infra/-/settings/ci_cd) 
to get the password for the `Gigadb` AWS IAM user.
2. Use the `Gigadb` AWS IAM user credentials to log into the AWS console.
3. Go to the [Elastic IP addresses page](https://ap-east-1.console.aws.amazon.com/ec2/v2/home?region=ap-east-1#Addresses:)
4. Check there is an elastic IP address with the Name, `eip-gigadb-live-gigadb`.

