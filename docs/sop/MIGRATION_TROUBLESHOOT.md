# Troubleshooting guide for migrating gigadb.org to GigaDB instance on AWS

## TLS certificates

Having gigadb.org pointing to GigaDB running on AWS requires this instance to
have a new TLS certificate that has been issued for the gigadb.org domain. When 
migration of beta.gigadb.org to gigadb.org was attempted, however, the existing
certificate was only issued for beta.gigadb.org which led to a name mismatch SSL
error because the SSL certificate did not match the URL that your browser was
accessing.

To facilitate switching between beta.gigadb.org and gigadb.org whilst we are
resolving migration problems, a temporary SSL certificate has been created which
works with beta.gigadb.org, gigadb.org and portainer.gigadb.org domains. The
procedure of how this TLS certificate was created is documented below.

Log into live web ec2 instance:
```
$ cd ops/infrastructure/envs/live
$ ssh -i ~/.ssh/id-rsa-aws-hk-gigadb.pem -o ProxyCommand="ssh -W %h:%p -i ~/.ssh/id-rsa-aws-hk-gigadb.pem centos@ec2_bastion_public_ip" centos@ec2_private_ip
```

When you list available docker images, you will see a certbot docker image:
```
[centos@ip-10-99-0-229 ~]$ docker images
REPOSITORY                                                                           TAG       IMAGE ID       CREATED         SIZE
registry.gitlab.com/gigascience/upstream/gigadb-website/production_app               live      5471da52eb3e   13 hours ago    611MB
registry.gitlab.com/gigascience/upstream/gigadb-website/production_web               live      2e84a1281100   13 hours ago    111MB
portainer/portainer-ce                                                               latest    7536f6a42542   5 months ago    294MB
certbot/certbot                                                                      latest    28ba94451daa   5 months ago    115MB
registry.gitlab.com/gigascience/upstream/gigadb-website/production_tideways-daemon   live      c51e5a87bce7   11 months ago   116MB
registry.gitlab.com/gigascience/upstream/gigadb-website/production_config            live      669e50b0e226   22 months ago   15.3MB
```

Certbot is a free, open source tool for creating Let's Encrypt certificates that
can be used by websites to enable HTTPS. We can create and run a new container
using certbot docker image:
```
[centos@ip-10-99-0-229 ~]$ docker run -it certbot/certbot:latest --version
certbot 2.7.4
```

The keys and certificates created by Certbot can be found in `/etc/letsencrypt/live$domain`.
We need to mount this Docker container directory with a directory on the host
machine. Let's create 3 new directories for this:
```
[centos@ip-10-99-0-229 ~]$ mkdir -p /home/centos/data/certbot/letsencrypt
[centos@ip-10-99-0-229 ~]$ mkdir -p /home/centos/data/certbot/www
[centos@ip-10-99-0-229 ~]$ mkdir -p /home/centos/data/certbot/log
```

There are rate limits to the number of certificates that Let's Encrypt will
allow you to create. Over-running this limit means you will need to wait for a
week before you will be allowed to create more certificates. For this reason, 
use the Let's Encrypt test staging API to learn how to create TLS certificates
by using `--test-cert` flag:
```
[centos@ip-10-99-0-229 ~]$ docker run -it --rm --name temp_certbot \
  -v /home/centos/data/certbot/letsencrypt:/etc/letsencrypt \
  -v /home/centos/data/certbot/www:/tmp/letsencrypt \
  -v /home/centos/data/certbot/log:/var/log \
  certbot/certbot:latest \
  certonly --test-cert --manual \
  --preferred-challenges dns \
  -d gigadb.org -d beta.gigadb.org -d portainer.gigadb.org \
  --email tech@gigasciencejournal.com
```

To create TLS certificates for use in live production site:
```
# -rm remove container when it exits, -it starts interactive shell
# Uses DNS authentication before cert creation
[centos@ip-10-99-0-229 ~]$ docker run -it --rm --name temp_certbot \
  -v /home/centos/data/certbot/letsencrypt:/etc/letsencrypt \
  -v /home/centos/data/certbot/www:/tmp/letsencrypt \
  -v /home/centos/data/certbot/log:/var/log \
  certbot/certbot:latest \
  certonly --manual \
  --preferred-challenges dns \
  -d gigadb.org -d beta.gigadb.org -d portainer.gigadb.org \
  --email tech@gigasciencejournal.com
```

We used [manual DNS authentication](https://eff-certbot.readthedocs.io/en/stable/using.html#manual)
since gigadb.org is pointing to CNGB instance of GigaDB. The cerbot tool will 
request that you place TXT DNS record with specific contents under the domain 
name consisting of the hostname for which you want a certificate issued, 
prepended by `_acme-challenge`. You will need to access the [Alibaba DNS manager](https://www.alibabacloud.com)
to do this because the gigadb.org domain is managed by this service.

The new certificates can be viewed in the `/home/centos/data/certbot/letsencrypt/live/gigadb.org`
directory:
```
[centos@ip-10-99-0-229 ~]$ cd /home/centos/data/certbot/letsencrypt/live/gigadb.org
[centos@ip-10-99-0-229 ~]$ ls
README  cert.pem  chain.pem  fullchain.pem  privkey.pem
```

You can check your certificates have been created by going to https://crt.sh/?q=gigadb.org.

To allow beta.gigadb.org and gigadb.org to use these certificates, the contents
were copied into the values of the appropriate live Gitlab secret variables.
Deployment of GigaDB using `ld_gigadb` step in the CI/CD pipeline will pull
these certificates into the live web ec2 server.

