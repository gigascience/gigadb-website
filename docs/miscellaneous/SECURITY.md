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

We chose the intermediate configuration for now as it provides good security while allowing most of web audience to access our site.
I think (we will need to confirm this by analysing our web analytics) the modern profile would prevent some of our visitors not using the most recent version of web browsers/OS combo to visit our website.

We have also used Qualys' SSL Labs [5] to audit our TLS termination setup from a security perspective.
Their automated audit checks if our setup is vulnerable to the common TLS attacks and exploits.

After three attempts and making the corresponding corrections to our configuration, we got the highest grade A+. The report also lists
the common vulnerabilities and whether we are vulnerable to them or not [6].

### OpenSSL

Docker, certbot, Nginx and most tools on Linux rely on the open source library OpenSSL to work with TLS certificates.
We've upgraded it to a recent version (1.1.1) to ensure vulnerabilities associated with older versions are not problem.

## Content Security Policy

Content Security Policy (CSP) is a web standard from the W3C [7] aimed at providing web site owners a mechanism to tell web browsers the rules
for allowing and denying  execution of resources when a user navigates to the owner's web site.

### How to setup

Two headers are used in ``ops/configuration/nginx-conf/sites/nginx.target_deployment.https.conf.dist``, in the ``Security headers`` section of the ``server`` block.:

* ``Content-Security-Policy`` : validated policies that reduce attack surface without breaking the web site
* ``Content-Security-Policy-Report-Only``: policies we want to apply to further reduce attack surface but cannot yet as they may break the website

Make sure each of those headers are present only once. See below for guidance on how to fill in their values.

### How it works

The policies are defined in the web server and executed in compatible web browsers [8].
The goal is to mitigate certain types of attacks, especially XSS ; and to complement other security mechanisms
such as CORS or HSTS.

>more info:
>
>* https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP
>* https://owasp.org/www-community/controls/Content_Security_Policy
>* https://developers.google.com/web/fundamentals/security/csp?utm_source=devtools#source_allowlists
f
Although all resources hosted by the websites and external resources referred to in the HTML and javascript code are concerned,
we are particularly concerned with external links as those involve executing several external resources (javascript, CSS, ...).

The aforementioned rules are called policies and are specified in response headers from the web servers using a specific configuration syntax

>See cheatsheet here: https://scotthelme.co.uk/csp-cheat-sheet/

It can be tricky to define CSP policies, and they are two types of challenges that can lead to either failure in reducing attack surface and/or breakage of website functionalities:

* policy misconfiguration
* too permissive policies

>Note: this blog post is a good illustration of those pitfalls: https://www.troyhunt.com/how-to-break-your-site-with-content/

To help with the policy definition process, the CSP specification has two modes, a reporting-only mode and an enforcement mode.
The former will have web browsers to only report violations, while the latter mode will have the web browsers to actually block the resources in violation of policies.
It is recommended to start with report-only mode, then switch to enforcement when we fixed the violations form our own resources or authorised resources.
The two modes can be used at the same time, for example when we want to introduce a new policy for a new type of external link, we define that policy in a report-only header, while all previously defined policies stay in their existing policy enforcement header.

A feature of CSP is the possibly to specify the url of a service where to send policy violation report. It can be a self provisioned service or an external service.
For GigaDB, I have created an account (it's free and trustworthy) at ReportURI [9] which offers detailed, nicely navigable reports (amongst other features)

## References

[1] https://nmap.org/book/man-port-specification.html

[2] http://www.iana.org/assignments/service-names-port-numbers/service-names-port-numbers.xhtml

[3] https://www.linux.com/topic/networking/introduction-ss-command/

[4] https://ssl-config.mozilla.org

[5] https://www.ssllabs.com/ssltest/index.html (make sure to tick "Do not show the results on the boards")

[6] https://drive.google.com/file/d/1NAcZvUNvnxhiM4g5KPNeAmqdk5v8KZvy/view?usp=sharing

[7] https://www.w3.org/TR/CSP/

[8] https://caniuse.com/contentsecuritypolicy2

[9] https://report-uri.com