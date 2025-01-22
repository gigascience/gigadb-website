# Mkdocs setup

[MkDocs](https://www.mkdocs.org/) is a static site generator for building a project documentation page, and [Material for MkDocs](https://squidfunk.github.io/mkdocs-material/getting-started/) is a well known third party theme for MkDocs.

Documentation source files should be written in Markdown, and configuration is done in the `mkdocs.yml` file.

### Prerequisites

In order to spin up mkdocs page, you need to docs directory in the root of the project, and the `mkdocs.yml` file in the root of the project, for example:

```bash
$ ls -l 
drwxr-xr-x@ 11 kencho  staff     352 Jan 22 10:11 docs
-rw-r--r--@  1 kencho  staff     841 Jan 22 10:20 mkdocs.yml
$ ls -l docs 
total 16
drwxr-xr-x@ 11 kencho  staff   352 Jan 20 11:58 awsdocs
drwxr-xr-x@  4 kencho  staff   128 Jan 14 13:59 curators
drwxr-xr-x@ 10 kencho  staff   320 Jan 22 11:03 developers
-rw-r--r--@  1 kencho  staff  2124 Jan 22 10:11 index.md
-rwxr-xr-x@  1 kencho  staff   274 Nov  4 14:27 make_phpdoc
drwxr-xr-x@  8 kencho  staff   256 Jan 20 12:00 miscellaneous
drwxr-xr-x@  3 kencho  staff    96 Nov  4 14:27 prs
drwxr-xr-x@ 22 kencho  staff   704 Jan 21 09:58 sop
drwxr-xr-x@ 15 kencho  staff   480 Nov  4 14:27 wasabidocs
```

### Installation 

Install mkdocs. On mac you can use brew:

```
$ brew install mkdocs
```

Otherwise you can use Python pip:

```
pip install mkdocs
```

To start the server, from this project root directory, run the command:

```
$ cd gigadb-website/
$ mkdocs serve
```

the documentation will be available at [http://127.0.0.1:8000](http://127.0.0.1:8000), as it by default will be served at port 8000.

### Dockerize the mkdocs page and using the mkdocs-material theme

To dockerize the mkdocs page, you can use the `squidfunk/mkdocs-material` image from dockerhub, and mount the current directory to the container to serve the page:

```
$ cd gigadb-website/
$ docker run --rm -it -p 8009:8000 -v ${PWD}:/docs squidfunk/mkdocs-material
or
$ docker-compose up -d mkdocs
```

the documentation page will be available locally at [http://localhost:8009/](http://localhost:8009/), as it will be served at port 8009.

In this approach, mkdocs page with mkdocs-material theme can be spun up in a containerized environment without the need to install it locally.

The benefit of using this theme is that it has better documentation in how to configure the page as in [here](https://squidfunk.github.io/mkdocs-material/setup/), also it has a more modern look and feel.

It is responsive, so it will look good on mobile devices as well.

### Deploying the documentation site

The contaierized service is defined in the `ops/deployment/docker-compose.production-envs.yml` file with its dockerfile at `ops/packaging/Production-Mkdocs-Dockerfile`
which with base image from https://github.com/squidfunk/mkdocs-material and will only copy existing `docs/` and the config file `mkdocs.yml` to the container for spinning up the page.

At first,create a new CNAME record in the DNS settings of the domain registrar, eg. `docs.${DOMAIN_NAME}`, pointing to the web server of the production environment.

Then create a new server block in nginx config which will `proxy_pass` all the traffic of the mkdocs container to the `docs.${DOMAIN_NAME}` via the port 8000.

Then generate the SSL certificate for the domain `docs.${DOMAIN_NAME}` using certbot, and configure the server block to use the SSL certificate, as a result, the page can be accessed through https protocol.

The documentation page will be accessible to https://docs.${DOMAIN_NAME} after deployment through the CI/CD pipeline as one of the containerized services
in the production environments, eg. https://docs.gigadb.org/ or https://docs.staging.gigadb.org.org/ .



