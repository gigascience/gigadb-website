# Ports

##  Ports used by GigaDB on its infrastructure

| Port number | Protocol/Service | open on servers |
| --- | --- | --- |
| 80 | HTTP | dockerhost | 
| 443 | HTTPS | dockerhost |
| 2375 | Docker | dockerhost (local dev only) |
| 2376 | Docker | dockerhost (AWS deployments only) |
| 9000 | PHP-FPM | dockerhost (not exposed to host on AWS deployments) |
| 9009 | Portainer | dockerhost |
| 5432 | PostgresQL | AWS RDS |
| 22 | sshd | dockerhost and bastion |
| 21 | FTP | CNGB FTP |


## Ports open by Operating system services in Centos 8.4

| Port number | Protocol/Service | open on servers |
| --- | --- | --- |
| 111 | rpcbind | dockerhost and bastion | 
| 323 (UDP) | chronyd | dockerhost and bastion |
| 22 | sshd | dockerhost and bastion |

## Scanning for open ports

On local dev environment one can use netcat
```
$ docker-compose run --rm test bash -c 'nc -z -v <host> 1-65535' | grep succeeded
```
>Note: upper boundary is 65535 because port number is stored in a 16-bit field in TCP packets

For testing on AWS deployment, one can ssh into dockerhost and/or bastion and run the scan from there.
However in that environment ``netcat`` is very slow, and it's better to use ``nmap``[1] instead.


```
$ sudo dnf install nmap
$ nmap -Pn <host>
```
in ``<host>``, do use the private IP address of the host to be audited, so that the network traffic 
stays within the VPC.


The above command will scan the top 1000s most common ports registered with IANA [2].
If a protocol/service is too recent (e.g: Docker), it won't be in there.

In such cases, use ``-p`` parameters to pass on a list of ranges.

```
$ nmap -Pn -p 2300-2400,8000-11000 11.99.0.252
```

However, the most reliable way to know what ports are open on a linux server, 
is to ssh into the target host and run the system command ``ss`` [3]

```
$ ss -tunlp
```

## TLS vulnerabilities and mitigations

Our project use TLS certificate infrastructure for encrypting user requests to our web site.

We used the Mozilla SSL configuration tool [4] to help us generate a safe nginx configuration instructions for a TLS termination proxy.
That tool can be configured using a choice of three profiles

| profile | description | security level |
| --- | --- | --- | 
| Modern | "Services with clients that support TLS 1.3 and don't need backward compatibility" | highest |
| Intermediate | "General-purpose servers with a variety of clients, recommended for almost all systems" | high |
| Old | "Compatible with a number of very old clients, and should be used only as a last resort" | low |

We chose the intermediate configuration as it provides good security and doesn't have too strong requirements on website visitor's up-to-date-ness of their browser.
I think the Modern profile would prevent the visitors not using the most recent version of web browsers to visit our website.

We have also used Qualys' SSL Labs [5] to audit our TLS termination setup from a security perspective.
Their automated audit checks if our setup is vulnerable to the common TLS attacks and exploits.

After three attempts and making the corresponding corrections to our configuration, we got the highest grade A+. The report also lists
the common vulnerabilities and whether we are vulnerable to them or not [6].



[1] https://nmap.org/book/man-port-specification.html

[2] http://www.iana.org/assignments/service-names-port-numbers/service-names-port-numbers.xhtml

[3] https://www.linux.com/topic/networking/introduction-ss-command/

[4] https://ssl-config.mozilla.org

[5] https://www.ssllabs.com/ssltest/index.html (make sure to tick "Do not show the results on the boards")

[6] https://drive.google.com/file/d/1NAcZvUNvnxhiM4g5KPNeAmqdk5v8KZvy/view?usp=sharing
