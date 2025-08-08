## Manual setup of access to Hong Kong EFS from Sydney infrastructure


The steps are identical for production staging and live environments.
From the Sydney staging environment, we want access to Hong Kong’s staging environment.
From the Sydney live environment, we want access to Hong Kong’s live environment.
The following instructions worksfor both developer fork deployments and Upstream deployments.

## Step 1:  Log in to the Sydney bastion server as “ec2-user” on the environment of choice (staging or live) and create the `/share/dropbox` directory

```
mkdir /share/dropbox
```

## Step 2: Create a new SSH key pair for the chosen environment

staging:

```
ssh-keygen -t rsa -f /home/ec2-user/.ssh/hk_staging_access
cat ~/.ssh/hk_staging_access.pub
```

Live:

```
ssh-keygen -t rsa -f /home/ec2-user/.ssh/hk_live_access
cat ~/.ssh/hk_live_access.pub
```

Copy the appropriate public key content

## Step 3: Log in to the Hong Kong bastion server of the same environment (staging or live) and add the public key to the authorized_keys file

Staging:

```
ssh -i <key-to-ap-east-1>  centos@bastion-stg.gigadb.host
echo “<previously copied content of staging public key generated in previous step> >> ~/.ssh/authorized_keys
```

Live:

```
ssh -i <key-to-ap-east-1>  centos@bastion.gigadb.host
echo “<previously copied content of live public key generated in previous step> >> ~/.ssh/authorized_keys
```


>Note: Make sure you use `>>` so you don't obliterate regular SSH access

## Step 4: Log in again to the Sydney bastion server and add an entry to the rclone config


Staging:

```
sudo vi .config/rclone/rclone.conf
[hk_efs_staging]
type = sftp
host = bastion-stg.gigadb.host
user = centos
key_file = /home/ec2-user/.ssh/hk_staging_access
shell_type = unix

```

Live:

```
sudo vi .config/rclone/rclone.conf
[hk_efs_live]
type = sftp
host = bastion.gigadb.host
user = centos
key_file = /home/ec2-user/.ssh/hk_live_access
shell_type = unix

```


## Step 5: Log in again to the Sydney bastion server and mount the directory from the Hong Kong bastion server where the user dropboxes are located


Staging:

```
rclone mount hk_efs_staging:/share/dropbox /share/dropbox &
```

Live:

```
rclone mount hk_efs_live:/share/dropbox /share/dropbox &
```

