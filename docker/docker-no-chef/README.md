# Using Docker to run one process per container

```bash
vagrant up
vagrant ssh
cd /vagrant/docker/docker-no-chef
docker-compose up
```
Use browser to check [http://192.168.42.10:8080](http://192.168.42.10:8080)

To test the php container, point your browser to http://192.168.42.10:8080/index.php

# Start postgres instance

```bash
# Start postgres instance
docker run --name some-postgres -e POSTGRES_PASSWORD=mysecretpassword -d postgres
# Connect to it via psql
docker run -it --rm --link some-postgres:postgres postgres psql -h postgres -U postgres
Password for user postgres: 
psql (10.1)
Type "help" for help.

postgres=# 
```

# Connect to Postgres
$ docker exec -it 05b3a3471f6f bash
bash-4.3# psql -U gigadb
psql (9.4.15)
Type "help" for help.

gigadb=# \l
                                 List of databases
   Name    |  Owner   | Encoding |  Collate   |   Ctype    |   Access privileges   
-----------+----------+----------+------------+------------+-----------------------
 gigadb    | postgres | UTF8     | en_US.utf8 | en_US.utf8 | 
 postgres  | postgres | UTF8     | en_US.utf8 | en_US.utf8 | 
 template0 | postgres | UTF8     | en_US.utf8 | en_US.utf8 | =c/postgres          +
           |          |          |            |            | postgres=CTc/postgres
 template1 | postgres | UTF8     | en_US.utf8 | en_US.utf8 | =c/postgres          +
           |          |          |            |            | postgres=CTc/postgres
(4 rows)

gigadb=# 



