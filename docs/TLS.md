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

| Name on filesystem | Name in GitLab variables | role | nginx directive |
| --- | --- | --- | --- |
| fullchain.pem |tls_fullchain_pem| Signed certificate and intermediates | ``ssl_certificate``|
| chain.pem |tls_chain_pem| Root CA certificate plus intermediates| ``ssl_trusted_certificate``|
| privkey.pem |tls_privkey_pem|Private key for the certificate|``ssl_certificate_key``|

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
which is executed when the ``ops/infrastructure/dockerhost_playbook.yml`` playbook is run.

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
