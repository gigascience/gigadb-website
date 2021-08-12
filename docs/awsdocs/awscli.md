# AWS Command Line Interface

The AWS command line interface (CLI) is required to decode error messages that 
are provided by the AWS management console when there is a problem with 
performing an operation.

## Installation

Information to install AWS CLI is available 
[here](https://docs.aws.amazon.com/cli/latest/userguide/cli-chap-install.html).

## Configuration

AWS CLI requires configuration to use your AWS account:

`~/.aws/config`
```
[default]
region=ap-east-1
output=json

[profile Username]
region=ap-east-1
output=json
```

`~/.aws/credentials`
```
[default]
aws_access_key_id=AKIAIOSFODNN7EXAMPLE
aws_secret_access_key=wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY

[Username]
aws_access_key_id=ASDSFSDFFEXAMPLE
aws_secret_access_key=eDFDSG$dgFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY
```

To test your configuration:
```
$ aws configure list --profile Username
      Name                    Value             Type    Location
      ----                    -----             ----    --------
   profile                    Username           manual    --profile
access_key     ****************YYGK shared-credentials-file    
secret_key     ****************EVDq shared-credentials-file    
    region                ap-east-1      config-file    ~/.aws/config
```

## Decoding error messages

If an error message is displayed on your AWS management console, it can be 
decoded as follows:
```
$ aws sts --profile Username decode-authorization-message --encoded-message (encoded error message) --query DecodedMessage --output text | jq '.'

```