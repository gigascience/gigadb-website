# Getting Ubuntu vagrant VM to work

```bash
$ git submodule sync
$ git submodule update --init
$ vagrant up --no-provision
$ vagrant ssh

# you're now executing commands inside vagrant VM's shell

# Must install chef 11 client
=> wget 'https://opscode-omnibus-packages.s3.amazonaws.com/ubuntu/10.04/i686/chef_11.18.6-1_i386.deb'
=> sudo dpkg -i 'chef_11.18.6-1_i386.deb'
# then go back to your normal terminal and run `vagrant provision`, then `vagrant ssh` again

# For whatever stupid reason, I can't use chef's `nginx_site` to disable it.
# Gotta do it manually for now.
=> sudo rm /etc/nginx/sites-enabled/default

# If you don't do a db dump from dev3 it will fail.
=> cd /vagrant
=> . protected/scripts/set_env.sh
=> psql gigadb < giga.sql  # assuming giga.sql is a dump you got from dev3

=> exit
```

# If you want to use centos vagrant box instead

You need to have the environment varible `$GIGADB_BOX` set to `centos`:

```bash
$ git submodule sync
$ git submodule update --init
$ export GIGADB_BOX='centos'
$ vagrant up
$ vagrant ssh

# you're now executing commands inside vagrant VM's shell

# If you don't do a db dump from dev3 it will fail.
=> cd /vagrant
=> . protected/scripts/set_env.sh
=> psql gigadb < giga.sql  # assuming giga.sql is a dump you got from dev3

=> exit
```

# Deploy on dev3

This goes in `/etc/chef/node.json`:

```json
    "gigadb": {
        "db": {
            "database": "gigadb_dev",
            "host": "server IP address",
            "password": "database user password",
            "port": "5432",
            "user": "database username"
        },
        "log_dir": "/var/www/hosts/gigadb.cogini.com/logs",
        "server_names": [
            "gigadb.cogini.com"
        ],
        "site_dir": "/var/www/hosts/gigadb.cogini.com/htdocs",
        "root_dir": "/var/www/hosts/gigadb.cogini.com/htdocs"
    },

    "elasticsearch": {
        "version": "1.3.4"
    },

    "java": {
        "install_flavor": "oracle",
        "jdk_version": "7",
        "oracle": {
            "accept_oracle_download_terms": true
        }
    },

    "nodejs": {
        "version": "0.10.33"
    }
```

Pull latest version then simply run `chef-solo` to provision

```bash
$ cd /var/www/hosts/gigadb.cogini.com/htdocs
$ sudo -u gigadb git pull  # make sure you're on master first, of course.
$ sudo chef-solo -c /etc/chef/solo.rb -j /etc/chef/node.json -o 'role[server],gigadb::default'
# or you can remove the `role[server],` part to save time.
```

# Deploy on prod

*a giant TODO here*

# Deploy on centos6.4 (gigadb's testing server)

Must specify postgresql directory (default `dir` value in postgresql cookbook is
`var/lib/pgsql/data`, which is wrong):

```json
    "postgresql": {
        "version": "9.1",
        "dir": "/var/lib/pgsql/9.1/data"
    }
```

After deployment:

- Remove '/etc/nginx/conf.d/default.conf'. That's the "Welcome to nginx" site, which overshadows
  our site.
