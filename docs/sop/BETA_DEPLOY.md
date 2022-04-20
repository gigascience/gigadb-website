# SOP: Deploying to beta.gigadb.org

A deployment of the GigaDB website code in the `Upstream` Gitlab group to the
`live` environment provides the website that is located at beta.gigadb.org.

## Check elastic IP address for live environment

The beta.gigadb.org domain name has been allocated with an AWS elastic IP 
address. You can check the current elastic IP address pointing to 
beta.gigadb.org from the AWS EC2 console:

1. Go to the [cnhk-infra CI/CD variables page](https://gitlab.com/gigascience/cnhk-infra/-/settings/ci_cd) 
to get the password for the `Gigadb` AWS IAM user.
2. Use the `Gigadb` AWS IAM user credentials to log into the AWS console.
3. Go to the [Elastic IP addresses page](https://ap-east-1.console.aws.amazon.com/ec2/v2/home?region=ap-east-1#Addresses:)
4. Check there is an elastic IP address with the Name, `eip-gigadb-live-gigadb`.

## Check domain name resolution to beta.gigadb.org

Resolution to beta.gigadb.org with the `eip-gigadb-live-gigadb` elastic IP 
address requires a DNS A record. Check this has been created in Alibaba Cloud
console:

1. Go to the [cnhk-infra CI/CD variables page](https://gitlab.com/gigascience/cnhk-infra/-/settings/ci_cd)
to get the `Alibaba_user_email` and `Alibaba_user_password` credentials.
2. Log into the [Alibaba Cloud console](https://account.alibabacloud.com/login/login.htm?oauth_callback=https%3A%2F%2Fhome-intl.console.aliyun.com%2F%3Fspm%3Da3c0i.7911826.6791778070.41.44193870AxVzyk&lang=en) using the above credentials.
3. You will be asked for a 6 digit number that is provided by the linked
Google Authenticator app.
4. Once logged into the console, go to the Domain Names page
5. You will see an entry for `gigadb.org` domain - click on this
6. You will now see the `DNS Settings gigadb.org` page. There should be an entry
for the `beta` Host with a value equal to the `eip-gigadb-live-gigadb` elastic 
IP address.
7. If there is no `beta` Host then this DNS A record should be created.
