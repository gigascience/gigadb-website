# TLS

TLS (transport layer security) is the communication encryption protocol used in a couple of places in our projects: communication to the website and communication to the Docker daemon


## TLS to encrypt connections to the web site

TLS is a mean to encrypt the communication between web browsers and our web site, 
in order to avoid interception, eavesdropping and leaking of PII (personally identifiable information) data.
It also reduces the risks of website and links hijacking into larger distributed nefarious activities. 

### How to set it up

1. The required input is a GitLab variable REMOTE_HOSTNAME that need to be defined in GitLab Variables for each production environment (staging and live).
2. Ensure that in the DNS server managing the REMOTE_HOSTNAME domain, there is a A record associating it with the public IP address of the target environment's dockerhost instance
3. The script ``ops/scripts/setup_cert.sh`` (executed during deployment from GitLab pipeline) will request  a TLS certificate signed and delivered by Let's Encrypt that will be written to the dockerhost's filesystem at a path accessible to Nginx container. The script will also save the certificate in GitLab variables.

When the infrastructure for the website has to be rebuilt, the ``ops/scripts/setup_cert.sh`` script will download the certificate from GitLab during the deployment to the newly rebuilt environment.

>If the IP address changes, the variables in GitLab that hold the certificate need to be deleted, and the certificate files on the filesystem need to be removed.

### How it works

That certificate cryptographically associates the fully qualified domain names (FQDN) REMOTE\_HOSTNAME and portainer.REMOTE\_HOSTNAME with the public IP address for the infrastructure that has been provisioned for the target environment with Terraform/Ansible tools.
In our case, the public IP address is an AWS Elastic IP.

It is then used by the web server/reverse proxy Nginx to encrypt communication from a web browser and API clients.

Certificates have limited lifetime, 3 months for Let's Encrypt emitted certificates.
Whenever a deployment is performed on staging or live, a renewal request will be performed by ``ops/scripts/setup_cert.sh``. Most of the time this request will be denied and that's expected behaviour as it's only when the expiration date is close that Let's Encrypt will accept the request for renewal.
Additionally, there is an automated check that run as part of the automated test suite that will fail if we are close (10 days or less) to 
the expiration date. 

Additionally, LetsEncrypt have a rate limit that limit the number of certificate that can be created for a domain.
That's the main reason we save them to GitLab variables, so that we can reuse them whenever we need to destroy and rebuild the dockerhost instance.

The interaction with Let's Encrypt is handled by Let's Encrypt command line tool ``cerbot`` which is run as a container service of the same name.

In our infrastructure, Nginx is called a TLS termination proxy and as such it needs to be configured 
in a certain way in order to accept TLS encrypted requests. 

The Nginx configuration follows the recommendation generated with the tool: https://ssl-config.mozilla.org (intermediate level)
which is the configuration tool associated with doc: https://wiki.mozilla.org/Security/Server_Side_TLS.
The nginx configuration is defined in template ``ops/configuration/nginx-conf/sites/nginx.target_deployment.https.conf.dist``

#### Certificate files

| Name on filesystem | Name in GitLab variables | role | nginx directive             |
| --- | --- | --- |-----------------------------|
| cert.pem |tls_cert_pem| Signed certificate | N/A                         |
| fullchain.pem |tls_fullchain_pem| Signed certificate and intermediates | ``ssl_certificate``         |
| chain.pem |tls_chain_pem| Root CA certificate plus intermediates| ``ssl_trusted_certificate`` |
| privkey.pem |tls_privkey_pem|Private key for the certificate| ``ssl_certificate_key``     |

#### Pre-requisite

Here are the manual steps for the developers who want to make certbot work just after freshly spinning up the dockerhost server.

1. Developer's production environments has been up and running as described in [SETUP_PROVISIONING.md](SETUP_PROVISIONING.md) and [SETUP_PROVISIONING.md](SETUP_CI_CD_PIPELINE.md)
2. Test the certbot commands manually to confirm:
From developer's production dockerhost server, the certbot certificates and  certbot renew command cannot work properly, because the file /etc/letsencrypt/renewal/$REMOTE_HOSTNAME.conf is not exist as mentioned in the [certbot documentation](https://eff-certbot.readthedocs.io/en/stable/using.html#configuration-file).
```
[ec2-user@ip-10-99-0-200 ~]$ docker run --rm -v kencho-gigadb-website_le_config:/etc/letsencrypt -v kencho-gigadb-website_le_webrootpath:/var/www/.le certbot/certbot certificates
Saving debug log to /var/log/letsencrypt/letsencrypt.log

- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
No certificates found.
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
```
3. But, actually the certs do exist in /etc/letsencrypt/archive/ec2-staging.gigadb.link and /etc/letsencrypt/live/ec2-staging.gigadb.link, for example:
```
[ec2-user@ip-10-99-0-200 ~]$ docker run --rm -v kencho-gigadb-website_le_config:/etc/letsencrypt -v kencho-gigadb-website_le_webrootpath:/var/www/.le registry.gitlab.com/gigascience/forks/kencho-gigadb-website/production_app:staging bash -c "ls -al /etc/letsencrypt/"
total 4
drwxr-xr-x. 5 root root   69 May 19 05:29 .
drwxr-xr-x. 1 root root   25 May 19 06:28 ..
drwxr-xr-x. 3 root root   37 May 15 13:54 archive
-rw-r--r--. 1 root root 1008 May 16 07:55 cli.ini
drwxr-xr-x. 3 root root   37 May 15 13:54 live
drwxr-xr-x. 5 root root   43 May 15 13:55 renewal-hooks
[ec2-user@ip-10-99-0-200 ~]$ docker run --rm -v kencho-gigadb-website_le_config:/etc/letsencrypt -v kencho-gigadb-website_le_webrootpath:/var/www/.le registry.gitlab.com/gigascience/forks/kencho-gigadb-website/production_app:staging bash -c "ls -al /etc/letsencrypt/archive/ec2-staging.gigadb.link"
total 16
drwxr-xr-x. 2 root root   83 May 15 13:55 .
drwxr-xr-x. 3 root root   37 May 15 13:54 ..
-rw-r--r--. 1 root root    5 May 15 13:54 cert1.pem
-rw-r--r--. 1 root root 1566 May 15 13:55 chain1.pem
-rw-r--r--. 1 root root 2973 May 15 13:54 fullchain1.pem
-rw-r--r--. 1 root root  241 May 15 13:55 privkey1.pem
[ec2-user@ip-10-99-0-200 ~]$ docker run --rm -v kencho-gigadb-website_le_config:/etc/letsencrypt -v kencho-gigadb-website_le_webrootpath:/var/www/.le registry.gitlab.com/gigascience/forks/kencho-gigadb-website/production_app:staging bash -c "ls -al /etc/letsencrypt/live/ec2-staging.gigadb.link"
total 0
drwxr-xr-x. 2 root root 79 May 15 13:55 .
drwxr-xr-x. 3 root root 37 May 15 13:54 ..
lrwxrwxrwx. 1 root root 58 May 15 13:54 cert.pem -> /etc/letsencrypt/archive/ec2-staging.gigadb.link/cert1.pem
lrwxrwxrwx. 1 root root 59 May 15 13:55 chain.pem -> /etc/letsencrypt/archive/ec2-staging.gigadb.link/chain1.pem
lrwxrwxrwx. 1 root root 63 May 15 13:54 fullchain.pem -> /etc/letsencrypt/archive/ec2-staging.gigadb.link/fullchain1.pem
lrwxrwxrwx. 1 root root 61 May 15 13:55 privkey.pem -> /etc/letsencrypt/archive/ec2-staging.gigadb.link/privkey1.pem
```

4. In order to make certbot work which requires a renewal config file, all the existing certs have to be removed first, which can be done by:

4.1 Delete all the existing certs by going to your gitlab pipeline and execute the job sd_teardown or ld_teardown.

OR

4.2 Manually in the dockerhost server as below:
```
[ec2-user@ip-10-99-0-200 ~]$ docker run --rm -v kencho-gigadb-website_le_config:/etc/letsencrypt -v kencho-gigadb-website_le_webrootpath:/var/www/.le registry.gitlab.com/gigascience/forks/kencho-gigadb-website/production_app:staging bash -c "rm -rf /etc/letsencrypt/live/*"
[ec2-user@ip-10-99-0-200 ~]$ docker run --rm -v kencho-gigadb-website_le_config:/etc/letsencrypt -v kencho-gigadb-website_le_webrootpath:/var/www/.le registry.gitlab.com/gigascience/forks/kencho-gigadb-website/production_app:staging bash -c "rm -rf /etc/letsencrypt/archive/*"
```


5. Create new certs and also the renewal config file

5.1 First delete the existing tls_cert_pem:$env, tls_chain_pem:$env, tls_fullchain_pem:$env and tls_privkey_pem:$env certs in your gitlab variable page.
Then go to gitlab pipeline and execute the job sd_gigadb or ld_gigadb to trigger the make_new_cert() in the setup_cert script.

OR

5.2 Manually in the dockerhost server as below: 
```
[ec2-user@ip-10-99-0-200 ~]$ docker run --rm -v kencho-gigadb-website_le_config:/etc/letsencrypt -v kencho-gigadb-website_le_webrootpath:/var/www/.le certbot/certbot certonly -d ec2-staging.gigadb.link -d portainer.ec2-staging.gigadb.link -d www.ec2-staging.gigadb.link
Saving debug log to /var/log/letsencrypt/letsencrypt.log
Account registered.
Requesting a certificate for ec2-staging.gigadb.link and portainer.ec2-staging.gigadb.link

Successfully received certificate.
Certificate is saved at: /etc/letsencrypt/live/ec2-staging.gigadb.link/fullchain.pem
Key is saved at:         /etc/letsencrypt/live/ec2-staging.gigadb.link/privkey.pem
This certificate expires on 2025-08-05.
These files will be updated when the certificate renews.
NEXT STEPS:
- The certificate will need to be renewed before it expires. Certbot can automatically renew the certificate in the background, but you may need to take steps to enable that functionality. See https://certbot.org/renewal-setup for instructions.

- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
If you like Certbot, please consider supporting our work by:
 * Donating to ISRG / Let's Encrypt:   https://letsencrypt.org/donate
 * Donating to EFF:                    https://eff.org/donate-le
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
[ec2-user@ip-10-99-0-200 ~]$ docker run --rm -v kencho-gigadb-website_le_config:/etc/letsencrypt -v kencho-gigadb-website_le_webrootpath:/var/www/.le registry.gitlab.com/gigascience/forks/kencho-gigadb-website/production_app:staging bash -c "ls -al /etc/letsencrypt/"
total 4
drwxr-xr-x. 7 root root  100 May  7 05:02 .
drwxr-xr-x. 1 root root   25 May  7 05:03 ..
drwx------. 3 root root   42 May  7 05:01 accounts
drwxr-xr-x. 3 root root   37 May  7 05:01 archive
-rw-r--r--. 1 root root 1008 May  7 03:56 cli.ini
drwxr-xr-x. 3 root root   51 May  7 05:01 live
drwxr-xr-x. 2 root root   42 May  7 05:01 renewal
drwxr-xr-x. 5 root root   43 May  7 03:47 renewal-hooks
[ec2-user@ip-10-99-0-200 ~]$ docker run --rm -v kencho-gigadb-website_le_config:/etc/letsencrypt -v kencho-gigadb-website_le_webrootpath:/var/www/.le registry.gitlab.com/gigascience/forks/kencho-gigadb-website/production_app:staging bash -c "ls -al /etc/letsencrypt/live/ec2-staging.gigadb.link"
total 4
drwxr-xr-x. 2 root root  93 May  7 05:01 .
drwxr-xr-x. 3 root root  51 May  7 05:01 ..
-rw-r--r--. 1 root root 692 May  7 05:01 README
lrwxrwxrwx. 1 root root  47 May  7 05:01 cert.pem -> ../../archive/ec2-staging.gigadb.link/cert1.pem
lrwxrwxrwx. 1 root root  48 May  7 05:01 chain.pem -> ../../archive/ec2-staging.gigadb.link/chain1.pem
lrwxrwxrwx. 1 root root  52 May  7 05:01 fullchain.pem -> ../../archive/ec2-staging.gigadb.link/fullchain1.pem
lrwxrwxrwx. 1 root root  50 May  7 05:01 privkey.pem -> ../../archive/ec2-staging.gigadb.link/privkey1.pem
[ec2-user@ip-10-99-0-200 ~]$ docker run --rm -v kencho-gigadb-website_le_config:/etc/letsencrypt -v kencho-gigadb-website_le_webrootpath:/var/www/.le registry.gitlab.com/gigascience/forks/kencho-gigadb-website/production_app:staging bash -c "cat /etc/letsencrypt/renewal/ec2-staging.gigadb.link.conf"
# renew_before_expiry = 30 days
version = 4.0.0
archive_dir = /etc/letsencrypt/archive/ec2-staging.gigadb.link
cert = /etc/letsencrypt/live/ec2-staging.gigadb.link/cert.pem
privkey = /etc/letsencrypt/live/ec2-staging.gigadb.link/privkey.pem
chain = /etc/letsencrypt/live/ec2-staging.gigadb.link/chain.pem
fullchain = /etc/letsencrypt/live/ec2-staging.gigadb.link/fullchain.pem

# Options used in the renewal process
[renewalparams]
account = 5dae3d3a4d6715434f7a00e8e308c761
rsa_key_size = 4096
pref_challs = http-01,
authenticator = webroot
webroot_path = /var/www/.le,
server = https://acme-v02.api.letsencrypt.org/directory
key_type = ecdsa
[[webroot_map]]
ec2-staging.gigadb.link = /var/www/.le
portainer.ec2-staging.gigadb.link = /var/www/.le
[ec2-user@ip-10-99-0-200 ~]$ docker run --rm -v kencho-gigadb-website_le_config:/etc/letsencrypt -v kencho-gigadb-website_le_webrootpath:/var/www/.le certbot/certbot certificates
Saving debug log to /var/log/letsencrypt/letsencrypt.log

- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
Found the following certs:
  Certificate Name: ec2-staging.gigadb.link
    Serial Number: 5848a2414b40988d18ada21700d68c94fd4
    Key Type: ECDSA
    Domains: ec2-staging.gigadb.link portainer.ec2-staging.gigadb.link
    Expiry Date: 2025-08-05 04:02:52+00:00 (VALID: 87 days)
    Certificate Path: /etc/letsencrypt/live/ec2-staging.gigadb.link/fullchain.pem
    Private Key Path: /etc/letsencrypt/live/ec2-staging.gigadb.link/privkey.pem
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
[ec2-user@ip-10-99-0-200 ~]$
```

6. Execute the renew --dry-run command

```
[ec2-user@ip-10-99-0-200 ~]$ docker run --rm -v kencho-gigadb-website_le_config:/etc/letsencrypt -v kencho-gigadb-website_le_webrootpath:/var/www/.le certbot/certbot renew
Saving debug log to /var/log/letsencrypt/letsencrypt.log

- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
Processing /etc/letsencrypt/renewal/ec2-staging.gigadb.link.conf
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
Certificate not yet due for renewal

- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
The following certificates are not due for renewal yet:
  /etc/letsencrypt/live/ec2-staging.gigadb.link/fullchain.pem expires on 2025-08-05 (skipped)
No renewals were attempted.
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
[ec2-user@ip-10-99-0-200 ~]$ docker run --rm -v kencho-gigadb-website_le_config:/etc/letsencrypt -v kencho-gigadb-website_le_webrootpath:/var/www/.le certbot/certbot renew --dry-run
Saving debug log to /var/log/letsencrypt/letsencrypt.log

- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
Processing /etc/letsencrypt/renewal/ec2-staging.gigadb.link.conf
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
Account registered.
Simulating renewal of an existing certificate for ec2-staging.gigadb.link and portainer.ec2-staging.gigadb.link

- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
Congratulations, all simulated renewals succeeded: 
  /etc/letsencrypt/live/ec2-staging.gigadb.link/fullchain.pem (success)
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
[ec2-user@ip-10-99-0-200 ~]$
```

7. Execute the renew command with deploy hook 
The `certbot renew` exit status will be 0 in the cases of successful renewal and renewal is not necessary as mentioned in the [certbot documentation](https://eff-certbot.readthedocs.io/en/stable/using.html#renewing-certificates),
the `--deploy-hook` will be executed after the certs are actually renewed successfully, and it will copy the certs to the dockerhost server and also update the gitlab variables with the new certs.

For the purpose of testing, we can use the `--dry-run` option to simulate the renewal process without actually renewing the certificate, and also appending `--run-deploy-hooks` to run the deploy hook even if the certificate is not renewed,
which will create a file `cert_renewed_successfully` in the `cert_renew_detection` folder on the dockerhost server.

```
[ec2-user@ip-10-99-0-200 ~]$ docker run --rm -v /home/ec2-user/cert_renew_detection:/renew_detect -v kencho-gigadb-website_le_config:/etc/letsencrypt -v kencho-gigadb-website_le_webrootpath:/var/www/.le certbot/certbot renew --deploy-hook "touch /renew_detect/cert_renewed_successfully" --dry-run --run-deploy-hooks
Saving debug log to /var/log/letsencrypt/letsencrypt.log

- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
Processing /etc/letsencrypt/renewal/ec2-staging.gigadb.link.conf
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

Simulating renewal of an existing certificate for ec2-staging.gigadb.link and portainer.ec2-staging.gigadb.link

- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
Congratulations, all simulated renewals succeeded: 
  /etc/letsencrypt/live/ec2-staging.gigadb.link/fullchain.pem (success)
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
[ec2-user@ip-10-99-0-200 ~]$ ls -al cert_renew_detection/
total 4
drwx--x--x. 2 ec2-user ec2-user   60 May  9 08:49 .
drwx------. 7 ec2-user ec2-user 4096 May  9 08:27 ..
-rw-r--r--. 1 root     root        0 May  9 08:49 cert_renewed_successfully
[ec2-user@ip-10-99-0-200 ~]$ 
```

#### Implementation of `renew_cert.sh` script

The above steps are implemented in the script `ops/scripts/renew_cert.sh` which will only serve the purpose of certbot renewal process.
The script will only work with the presence of the `/etc/letsencrpt/renewal/$REMOTE_HOSTNAME.conf` file, which can created by running the `certbot certonly` command as mentioned in the above step 5.
The script will also check if the certs are renewed successfully by checking the `cert_renewed_successfully` file in the `cert_renew_detection` folder on the dockerhost server.
With the presence of the `cert_renewed_successfully` file, the script will 
    1. restart the production web container, eg., `kencho-gigadb-website_web_1`  
    2. when the web container was restart successfully, the renewed the certs will be pushed to gitlab variable page and the symlinks will be created
    3. the file `cert_renewed_successfully` will then be removed to make the state clean.

#### How to run the `renew_cert.sh` script

1. Log in the dockerhost server and check
```
% ssh -i ~/.ssh/$aws.pem -o ProxyCommand="ssh -W %h:%p -i ~/.ssh/$aws.pem ec2-user@$bastion-public-ip" ec2-user@$dockerhost-private-ip
[ec2-user@ip-10-99-0-200 ~]$ docker images
REPOSITORY                                                                      TAG       IMAGE ID       CREATED         SIZE
registry.gitlab.com/gigascience/forks/kencho-gigadb-website/production_app      staging   b74424c979df   3 hours ago     619MB
registry.gitlab.com/gigascience/forks/kencho-gigadb-website/production_web      staging   9500e51aba4d   3 hours ago     90.6MB
certbot/certbot                                                                 latest    40464b56e4a7   4 weeks ago     114MB
registry.gitlab.com/gigascience/forks/kencho-gigadb-website/production_config   staging   9c1d06a5a6ac   20 months ago   15.3MB
[ec2-user@ip-10-99-0-200 ~]$ docker ps -a
CONTAINER ID   IMAGE                                                                                COMMAND                  CREATED       STATUS       PORTS                                                                                                NAMES
a26af6fe2162   portainer/portainer-ce:latest                                                        "/portainer -H unix:…"   3 hours ago   Up 2 hours   0.0.0.0:8000->8000/tcp, [::]:8000->8000/tcp, 9443/tcp, 0.0.0.0:9009->9000/tcp, [::]:9009->9000/tcp   kencho-gigadb-website_portainer_1
f4b7a0e26b86   registry.gitlab.com/gigascience/forks/kencho-gigadb-website/production_web:staging   "/docker-entrypoint.…"   3 hours ago   Up 2 hours   0.0.0.0:80->80/tcp, [::]:80->80/tcp, 0.0.0.0:443->443/tcp, [::]:443->443/tcp                         kencho-gigadb-website_web_1
e2adc376a5af   registry.gitlab.com/gigascience/forks/kencho-gigadb-website/production_app:staging   "docker-php-entrypoi…"   3 hours ago   Up 2 hours   9000/tcp, 9135/tcp                                                                                   kencho-gigadb-website_application_1
[ec2-user@ip-10-99-0-200 ~]$ docker volume ls
DRIVER    VOLUME NAME
local     kencho-gigadb-website_assets
local     kencho-gigadb-website_feeds
local     kencho-gigadb-website_le_config
local     kencho-gigadb-website_le_webrootpath
local     kencho-gigadb-website_portainer_data
[ec2-user@ip-10-99-0-200 ~]$ ls -al /usr/local/bin/renew_cert
-rwxr-xr-x. 1 ec2-user ec2-user 10061 May  6 06:59 /usr/local/bin/renew_cert
[ec2-user@ip-10-99-0-200 ~]$ cat .tls-certs-secrets
GIGADB_ENV=staging
CI_PROJECT_PATH=gigascience/forks/kencho-gigadb-website
REPO_NAME=kencho-gigadb-website
GITLAB_PRIVATE_TOKEN=xxxxxxxxxxxxxxxxxx
REMOTE_HOSTNAME=ec2-staging.gigadb.link
CI_API_V4_URL=https://gitlab.com/api/v4
[ec2-user@ip-10-99-0-200 ~]$ /usr/local/bin/renew_certs
Starting renew tls certs at 2025-05-09T08:28:23

Running on productions, using docker
Checking whether the certbot is configured correctly in local
certbot_configured_correctly: true

To see if they could be found in gitlab
fullchain_pem_remote_exists: true
privkey_pem_remote_exists: true
chain_pem_remote_exists: true
Renewing the certificate for ec2-staging.gigadb.link
Saving debug log to /var/log/letsencrypt/letsencrypt.log

- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
Processing /etc/letsencrypt/renewal/ec2-staging.gigadb.link.conf
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
Certificate not yet due for renewal

- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
The following certificates are not due for renewal yet:
/etc/letsencrypt/live/ec2-staging.gigadb.link/fullchain.pem expires on 2025-08-05 (skipped)
No renewals were attempted.
No hooks were run.
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
Certificate for ec2-staging.gigadb.link was not renewed!

[ec2-user@ip-10-99-0-200 ~]$
# Or you can redirect the sdtout and stderr to /var/log/renew_certs.log
[ec2-user@ip-10-99-0-200 ~]$ /usr/local/bin/renew_certs > /var/log/renew_certs.log 2>&1
[ec2-user@ip-10-99-0-200 ~]$ ls -al /var/log/renew_certs.log
-rw-------. 1 ec2-user root 7753 May  7 07:25 /var/log/renew_certs.log
```

#### Automated cert renewal process

The main purpose of the script is for the automated cert renewal process, for example, every Midnight on every Sunday, so the `renew` action will be executed weekly. The cronjob detail can be confirmed as:

```
[ec2-user@ip-10-99-0-200 ~]$ crontab -l
#Ansible: Renew TLS certificates on every Sunday at midnight
0 0 * * 0 /usr/local/bin/renew_certs > /var/log/renew_certs.log 2>&1
[ec2-user@ip-10-99-0-200 ~]$
```

All the stdout and the stderr will be stored at /var/log/renew_certs.log for further reference if needed.


#### File structure on dockerhost for certificate files

A docker volume ``le_config`` is used to store LetsEncrypt files, so that it can be mounted
to both the web container and the certbot container. The mount point in both case is ``/etc/letsencrypt``


```
$ docker exec rija-gigadb-website_web_1 ls -1l /etc/letsencrypt
total 4
drwxr-xr-x    3 root     root            41 Nov 16 10:04 archive
-rw-r--r--    1 root     root          1006 Nov 16 10:04 cli.ini
drwxr-xr-x    3 root     root            41 Nov 16 10:04 live
```

``cli.ini`` is the configuraiton for ``certbot`` and copied from ``ops/configuration/nginx-conf/le.(staging|live).ini``
in two stages:
1. ``Config-Dockerfile`` will copy those two files at the root of the Config container image
2. The ``config`` container service defined in ``ops/deployment/docker-compose-production-envs.yml`` has a command that copy one specific to current environment into final location as ``cli.ini``

``certbot`` will create the files in ``/etc/letsencrypt/archive/REMOTE_HOSTNAME`` and will create a symbolic link for each file in ``/etc/letsencrypt/live/REMOTE_HOSTNAME``

```
$ docker exec rija-gigadb-website_web_1 ls -1l /etc/letsencrypt/live/gigadb-staging.pommetab.com
total 0
lrwxrwxrwx    1 root     root            63 Nov 16 10:04 chain.pem -> /etc/letsencrypt/archive/gigadb-staging.pommetab.com/chain1.pem
lrwxrwxrwx    1 root     root            67 Nov 16 10:04 fullchain.pem -> /etc/letsencrypt/archive/gigadb-staging.pommetab.com/fullchain1.pem
lrwxrwxrwx    1 root     root            65 Nov 16 10:04 privkey.pem -> /etc/letsencrypt/archive/gigadb-staging.pommetab.com/privkey1.pem
```

Any application which needs access to the certificates (in our case Nginx) needs to reference the ``/etc/letsencrypt/live/REMOTE_HOSTNAME`` path.

Upon restoring them from GitLab variables,  ``ops/scripts/setup_cert.sh`` needs to write them in the   ``/etc/letsencrypt/archive/REMOTE_HOSTNAME`` path **AND** create the symlinks in ``/etc/letsencrypt/live/REMOTE_HOSTNAME`` path.


## TLS to encrypt connections between GitLab pipeline and the docker daemon

This is a mean to authenticate and encrypt communication from the GitLab pipeline to the docker daemon deployed on staging or live environment
for the purpose of configuring, starting and operating the application that is being deployed.
A secondary purpose is to remote control the docker demon deployed on staging and live from a local developer environment
for debugging purpose.

This usage requires the generation of a client certificate and of a server certificate.

### how to set it up

The certificates are created automatically as part of the provisioning of Docker with Ansible.
The client certificates are also saved to GitLab variables and to the operator's machine.


### how it works

The TLS certificate's files are generated by Ansible role [role-secure-docker-daemon](https://github.com/ansible/role-secure-docker-daemon)
which is executed when the ``ops/infrastructure/webapp_playbook.yml`` playbook is run.

Upon creation, the server certificates will be placed in ``etc/docker`` on the dockerhost,
while the client certificates will be placed in ``~/.docker``.

The ``ops/infrastructure/roles/docker-postinstall`` Ansible role is in charge of saving the client certificate files to GitLab variables
and to the local environment fo the Ansible's operator in ``ops/infrastructure/envs/(staging|live)/output-(dockerhost ip address)``.

These certificates don't have expiration date nor do they have rate limits as they are self-signed certificates created by the deployed instance of Docker.

#### Certificate files

| Name on filesystem | Name in GitLab variables | role | docker client/daemon argument |
| --- | --- | --- | --- |
| ca.pem | docker_tlsauth_ca|The self-generated CA (Certificate Authority) that will sign the server and client certificates | ``--tlscacert=``|
| server-cert.pem | n/a| server certificate| ``--tlscert=`` |
| server-key.pem |n/a| private key to server certificate| ``--tlskey`` | 
| cert.pem |docker_tlsauth_cert| client certificate | ``--tlscert`` |
| key.pem |docker_tlsauth_key| private key to client certificate| ``--tlskey`` |

Server example:

```
> dockerd \
    --tlsverify \
    --tlscacert=ca.pem \
    --tlscert=server-cert.pem \
    --tlskey=server-key.pem \
    -H=0.0.0.0:2376
```

Client example:

```
> docker --tlsverify \
    --tlscacert=ca.pem \
    --tlscert=cert.pem \
    --tlskey=key.pem \
    -H=$HOST:2376 ps
```
