# Ports in use by this project

## Docker infrastructure

software | Protocol | port | dev | staging | production
-------- | -------- | ---- | ----| ------- | ----------
Unsecured Docker | TCP | 2375 | Yes  | No | No
Secure Docker  | TCP | 2376 | No | Yes | Yes
Docker metrics | TCP | 9323 | Yes | Yes | yes
Docker Swarm Mode | TCP | 2377 | Planned | Planned | Planned
Docker node discovery | TCP+UDP | 7946 | Planned | Planned | Planned
Docker overlay network | UDP | 4789 | Planned | Planned | Planned

## Services

software | Protocol | port | dev | staging | production
-------- | -------- | ---- | ----| ------- | ----------
PostgresQL | TCP | 5432 | In container only | Yes | yes
Nginx HTTP | TCP | 80 | Yes | Yes | yes
Nginx HTTPS | TCP | 443 | Yes | Yes | yes
VsFTPd | TCP | 21 | In container only | Yes | yes
Redis | TCP | 6379 | In container only | In container only | In container only
Beanstalk | TCP | 11300 | In container only | In container only | In container only
SSH daemon | TCP | custom | No | Yes | Yes

