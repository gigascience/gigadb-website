# efs-filesystem

This module will provision an EFS resource in the selected region, as well as ancilliary resources (mount targets and access points) and associate the tags `deployment_target` and `owner` to the created resource.

## Input variables

| name | description |
| --- | --- |
| vpc | vpc object instanciated by the vpc module, needed to access the provisioned private subnets |
| deployment_target | target environment, used for taggging |
| owner | username of the developer, used for tagging | 

## Output used in main terraform files

| name | description |
| --- | --- |
| efs_filesystem_id | file system id |
| efs_filesystem_arn | arn of the filesystem |
| efs_filesystem_size_in_bytes | last know size of the EFS storage |
| efs_filesystem_access_points | maps of the available access points |


## Data sources

| Type | filter | description |
| --- | --- | --- |
| aws_region | current | return the current region |
| aws_availability_zones" | available | return list of all the availability zone in the current region |
| aws_caller_identity | current | return details about the current IAM user |


## Policies

policy for interaction with the EFS resources should be defined in IAM policy and documented in `docs/awsdocs/policy-efs.md`, like we do for everything else.

Therefore resource attached policy is disabled (`attach_policy = false`).

## File system type 

We use a **regional** type of storage

(for more info see https://docs.aws.amazon.com/efs/latest/ug/availability-durability.html#file-system-type)

It means, that to enable regional durability, we need to create a mount target associated to a private subnet in each availability zones of the selected region.
```
mount_targets              = { for k, v in zipmap(local.azs, var.vpc.private_subnets) : k => { subnet_id = v } }
```

## Performance profile

The settings `performance_mode` is set to `generalPurpose`

(for more info see https://docs.aws.amazon.com/efs/latest/ug/performance.html#performancemodes)

The settings `throughput_mode` is set to `elastic` (as recommended, but we need to keep an eye on that metric over time to see if that's stay the best mode for our usage)

(for more info see https://docs.aws.amazon.com/efs/latest/ug/performance.html#throughput-modes)


## Storage class

the module is configured with the following settings

| setting | value | description |
| --- | --- | --- |
| transition_to_ia | AFTER_30_DAYS | IA: Inactive data that is accessed only a few times each quarter | 
| transition_to_primary_storage_class | AFTER_1_ACCESS | Primary: Active data requiring fast sub-millisecond latency performance | 


We do not need to transition to archive (the third storage class) because dropbox are for transcient files (up to 3 months).

(for more info see https://docs.aws.amazon.com/efs/latest/ug/availability-durability.html#storage-classes)

## Other settings

Currently, the encryption-at-rest is disabled (`enable_backup_policy = false`) and so are the inter-region replication (`create_replication_configuration = false`) and the backup policy (`enable_backup_policy = false`).
They do not seem to be needed features for now but we need to have discussion with the business to confirm and wether the need could surface over time. There performance and cost factors associated with enabling those features.

