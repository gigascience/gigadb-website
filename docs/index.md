# GigaDB Documentation

This page contains the documentation for the GigaDB website. The documentation is written in Markdown and is built using [Material for MkDocs](https://squidfunk.github.io/mkdocs-material/getting-started/),
which is a well known third party theme for MkDocs, a static site generator geared towards project documentation.




## Start and accessing the documentation server using Docker

```
$ cd gigadb-website/
$ docker run --rm -it -p 8000:8000 -v ${PWD}:/docs squidfunk/mkdocs-material
```

the documentation will be available at: http://localhost:8000/

## Building the documentation site

To create a documentation web pages:

```
$ mkdocs build
$ ls site
about  fonts  index.html  license  search.html
css    img    js          mkdocs   sitemap.xml
```

## Deploying the documentation site

## 

