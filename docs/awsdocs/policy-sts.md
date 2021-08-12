# AWS permissions policy for security token service

Some AWS operations return an encoded message that provides details about an
authorization failure. This policy allows the user to decode the message and
has been added to the `developers` group.
```
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "VisualEditor0",
            "Effect": "Allow",
            "Action": "sts:*",
            "Resource": "*"
        }
    ]
}
```