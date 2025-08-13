# Setting up Xdebug with Docker and PHPStorm

This guide explains how to enable and use **Xdebug** in a **Docker** environment with **PHPStorm**

---

## 1. Configure Xdebug

You must configure it. You can do this in an `.ini` file in `docker/php/conf.d`:

```ini
#Xdebug 2
zend_extension=xdebug.so

[xdebug]
xdebug.remote_host=host.docker.internal
xdebug.remote_enable=1
xdebug.coverage_enable=1
xdebug.remote_log=/tmp/xdebug.log
xdebug.remote_autostart=1
xdebug.idekey=test
; Tell Xdebug how to reach your IDE
xdebug.remote_port=9003

#XDebug3 (PHP 8 images)
zend_extension=xdebug.so

[xdebug]
xdebug.mode=develop,debug,coverage
xdebug.client_host=host.docker.internal
xdebug.remote_log=/tmp/xdebug.log
xdebug.start_with_request=yes
xdebug.idekey=test
; Tell Xdebug how to reach your IDE
xdebug.client_port=9003


```

**Notes**
- `host.docker.internal` works on Docker Desktop (Windows/mac).
- On Linux, replace it with your host IP

## 2. Installing Xdebug (build-time ARG)

If your `Dockerfile` has:

```dockerfile
ARG INSTALL_XDEBUG=false

RUN if [ ${INSTALL_XDEBUG} = true ]; then \
  # Install the xdebug extension
  if [ $(php -r "echo PHP_MAJOR_VERSION;") = "5" ]; then \
    pecl install xdebug-2.5.5; \
  else \
    pecl install xdebug-2.9.8; \
  fi && \
  docker-php-ext-enable xdebug \
;fi
```

You can enable Xdebug installation by passing the build argument in `docker-compose.yml`:

```
  application:
    ...
    build:
      context: .
      dockerfile: ../packaging/Dockerfile
      args:
        ...
        - INSTALL_XDEBUG="true"
      volumes: 
        - ${APPLICATION}/docker/php/conf.d/xdebug.ini:/usr/local/etc/php/conf.d/xdebug.ini 
```

> Tip: If you want to keep Xdebug out of production, override INSTALL_XDEBUG only in your dev docker compose file.

Then rebuild: 

```bash
docker-compose build application
docker-compose up -d
```

## 3. Configure PHPStorm

### 3.1 Enable the debugger

- **Settings -> PHP -> Debug**
  - Debug port: **9003**
  - Check **can accept external connections**

### 3.2 Use the Docker PHP interpreter

- **Settings -> PHP**
  - CLI interpreter ->  click **...** -> click **+**
  - Choose: 
    - **From Docker**
    - Server: **Docker**
    - Configuration files: **./ops/deployment/docker-compose.yml**
    - Check connect to existing container: **application**
    - PHP executable: **PHP**
    - You should have **Debugger Xdebug 2.9.8**

### 3.3 Create a server and path mappings

- **Settings -> PHP -> Servers**
  - Name: `a_name`
  - Host: `gigadb.gigasciencejournal.com`
  - Port: `80`
  - Debugger: **Xdebug**
  - Enable **use path mappings** and map: your local project folder -> `/var/www`

## 4. Debug Workflow

1. Set a breakpoint in your PHP code.
2. In PHPStorm, click the green Debug icon with the right configuration (server + IDE key if needed)
3. PHPStorm will stop at the breakpoint



