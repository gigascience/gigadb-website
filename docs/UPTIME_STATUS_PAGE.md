# GigaDB server monitoring tool: UptimeRobot

UptimeRobot provides uptime monitoring service, its free tier plan allows up to 50 monitors with minimum monitoring interval `5mins` and 1 status page.
Currently, the GigaDB monitoring page is at [here](https://stats.uptimerobot.com/LGVQXSkN1y).

### How GigaDB servers are monitored

| Server name | Type of Check | value to check | 
| --- | --- | --- |
| GigaDB FTP server | HTTP(S) | https://ftp.cngb.org/pub/gigadb/pub/10.5524/ |
| GigaDB Web Site (LIVE) | HTTP(S) | https://gigadb.org |
| GigaDB Database Server (LIVE) | Port | database-live.gigadb.org:5432 |
| GigaDB Web Site (STAGING) | HTTP(S) | https://staging.gigadb.org |
| GigaDB Database Server (STAGING) | Port | database-staging.gigadb.org:5432 |
| GigaDB CI/CD Pipeline | HTTP(S) | https://gitlab.com/gigascience |
| GigaDB Codebase repository | HTTP(S) | https://github.com/gigascience/ |

### Where to find the login credentials for UptimeRobot dashboard?
The `uptime_robot_login_account` and `uptime_robot_login_password` are stored in [gitlab variable](https://gitlab.com/gigascience/cnhk-infra/-/settings/ci_cd) under gigascience repo.

### How to add monitor and create a status page?
Please refer to this [official doc](https://uptimerobot.com/faq/), which is up-to-date and easy to follow.