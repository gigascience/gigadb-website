# Software Architecture for File Upload Wizard

## Architecture

![File Upload Wizard Workflow](img/architecture.png)

## Systems Components

### Web Server

The web server is shared by all web apps, Gigadb and File Upload Wizard.
It also acts as a termination for TLS for the web apps, as well as to the TUS server.
The server software is Nginx opensource running on Alpine Linux.

### Database Server

The web server is shared by all web apps, Gigadb and File Upload Wizard.
PostgresQL 9.6 is used on non-production environment. It also run on Alpine Linux.

### TUS File server

### FTP Server

### Hooks

### Webapp

### Certificate management

## Containers

### web

### fuw-admin

### console

### database

### watcher

### tusd

### fuw-user

### watcher
