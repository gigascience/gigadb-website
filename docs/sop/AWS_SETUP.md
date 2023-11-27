### Verify AWS IAM policies

The `Gigadb` user needs the same AWS IAM policy permissions for accessing EC2,
RDS and S3 services as the developers. This can be checked by viewing the AWS
[IAM dashboard](https://us-east-1.console.aws.amazon.com/iamv2/home?region=us-east-1#/home).
The `Gigadb` user has been added to the `Applications` IAM group. This group
has the same permissions as those used by the developers.

> :warning: **You'll need your AWS admin user account to access the IAM console**

### Tools

Ensure `AWS CLI` has been installed, [click](https://docs.aws.amazon.com/cli/latest/userguide/getting-started-install.html) to see more details.

### Set up credentials for accessing AWS resources

> :warning: **Mistakes can happen with interchanging between AWS configurations below**

1. Save `id-rsa-aws-hk-gigadb.pem` available from [cnhk-infra CI/CD variables page](https://gitlab.com/gigascience/cnhk-infra/-/settings/ci_cd) into your `~/.ssh` directory.
2. Create a `known_hosts` file in `~/.ssh` if it does not already exist
```
$ touch ~/.ssh/known_hosts
```
3. Copy your `~/.aws/config` file into a new file:
```
$ cd ~/.aws
$ cp config config.ap-northeast-1
# Use this if you are based in France
$ cp config config.eu-west-3
```
3. Create an AWS config file to use for deploying to staging or
   beta.gigadb.org:
```
$ vi config.upstream
# The contents of config.upstream:
[default]
region=ap-east-1
output=json

[profile Gigadb]
region=ap-east-1
output=json
```
4. The staging and beta.gigadb.org websites are deployed in the ap-east-1 Hong
   Kong regions. You will need to copy `config.upstream` to a new `config` file to
   do this:
```
$ cp config.upstream config
```
> :warning: **You will need to overwrite the upstream `config` file with `config.ap-northeast-1` when returning to your development work**

5. Copy your `~/.aws/credentials` file into a new file:
```
$ cp credentials credentials.ap-northeast-1
# Use this if you are based in France
$ cp credentials credentials.eu-west-3
```

6. Create an AWS credentials file for deploying to staging or beta.gigadb.org:
```
$ vi credentials.upstream
# The contents of credentials.upstream:
[default]
aws_access_key_id=<aws_access_key_id for Gigadb user>
aws_secret_access_key=<aws_secret_access_key for Gigadb user>

[Gigadb]
aws_access_key_id=<aws_access_key_id for Gigadb user>
aws_secret_access_key=<aws_secret_access_key for Gigadb user>
```
7. Overwrite your current credentials file with the contents of
   `credentials.upstream`:
```
$ cp credentials.upstream credentials
```
> :warning: **You will need to overwrite the upstream `credentials` file with `credentials.ap-northeast-1` when returning to your development work**

Another option is to create a new `Gigadb` user account in your operating system
and only setting up the `Gigadb` AWS user configuration in it. This means you
will use this `Gigadb` operating system user for managing deployments to
staging and beta.gigadb.org.