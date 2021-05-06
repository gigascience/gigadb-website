# GigaDB Documentation


## Start and accessing the documentation server

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
$ mkdocs serve
```

the documentation will be available at: (http://127.0.0.1:8000)

## Building the documentation site

To create a documentation web pages:

```
$ mkdocs build
$ ls site
about  fonts  index.html  license  search.html
css    img    js          mkdocs   sitemap.xml
```

