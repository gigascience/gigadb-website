# GigaDB development

## Website

The GigaDB website is built using the [Yii](http://www.yiiframework.com)
PHP framework which provides it with a Model-View-Controller (MVC)
architecture.

Developing the website means that you will be writing new PHP code and
for this, you will need to use a text editor. For making simple changes,
a text-based editor will suffice such as [vi](https://en.wikipedia.org/wiki/Vi)
which comes pre-installed on Linux operating systems. However, you will
probably require a more user-friendly text editor for writing code in
the long term. You will need to decide which text editor is the most
suitable for your needs but I have heard good things about
[Sublime Text](https://www.sublimetext.com).

## Chef cookbooks

Occasionally, the operation of GigaDB and its website may require an 
application to be installed on its server platform. This application
should be provisioned using [Chef-Solo](https://docs.chef.io/chef_solo.html)
so that the installation process can be recorded as source code and 
readily automated as and when required. The procedure to do this is
demonstrated below using fail2ban as an example.

[Fail2ban](http://www.fail2ban.org) is an application that can protect a
server from malicious internet attacks by adding rules into the server's
firewall based on information recorded in log files.

Add the fail2ban cookbook to the [custom set of Chef cookbooks](https://github.com/pli888/chef-cookbooks)
that GigaDB uses:

```bash
$ mv fail2ban chef-cookbooks
```

Commit fail2ban into the repo:

```bash
$ cd chef/chef-cookbooks
$ git add fail2ban
$ git commit
```

Push fail2ban commit to origin:

```bash
$ git push origin`
```

Move to master branch of the chef-cookbooks directory:

```bash
$ cd chef/chef-cookbooks
$ git checkout master
```

Update the chef/chef-cookbooks submodule to fetch the fail2ban commit:

```bash
$ git fetch
remote: Counting objects: 44, done.
remote: Total 44 (delta 6), reused 6 (delta 6), pack-reused 38
Unpacking objects: 100% (44/44), done.
From https://github.com/pli888/chef-cookbooks
   797ad8d..0295ff9  master     -> origin/master
```

If you change directory out of the chef-cookbooks submodule and into the
gigadb-website repo, doing `git status` will now show that
chef-cookbooks has been modified due to the presence of fail2ban
cookbook. You therefore need to add, commit and
push the change in the chef-cookbooks so it can be used by the 
gigadb-website repo:

```bash
$ git add chef-cookbooks
$ git commit
$ git push origin
```

fail2ban can now be provisioned for GigaDB in a Chef recipe.

## Database

The data that the MVC architecture operates on is stored in a
PostgreSQL database. The schema for this database can be viewed by
opening the [gigadb_schema.svg](../sql/gigadb_schema.svg) file in a
web browser.

There are two ways of accessing the database in the Vagrant VM:

### Command-line database operation

The PostgreSQL database can be accessed using its `psql` command-line
client and the password `vagrant`

```bash
# Open a SSH session to the vagrant VM
$ vagrant ssh
$ psql -U gigadb -h localhost -W
Password for user gigadb:
psql (8.4.20)
Type "help" for help.

gigadb=>
```

This will enable you to use SQL commands to query the database:

```bash
gigadb=> select * from gigadb_user;
 id  |      email       |             password             | first_name | last_name | affiliation | role  | is_activated | newsletter | previous_newsletter_state | facebook_id | twitter_id | linkedin_id | google_id |    username     | orcid_id | preferred_link
-----+------------------+----------------------------------+------------+-----------+-------------+-------+--------------+------------+---------------------------+-------------+------------+-------------+-----------+-----------------+----------+----------------
 344 | admin@gigadb.org | 5a4f75053077a32e681f81daa8792f95 | Joe        | Bloggs    | BGI         | admin | t            | f          | t            |             |            |             |           | test@gigadb.org |          | EBI
 345 | user@gigadb.org  | 5a4f75053077a32e681f81daa8792f95 | John       | Smith     | BGI         | user  | t            | f          | t            |             |            |             |           | user@gigadb.org |          | EBI
(2 rows)

```

To quit from the pgsql session, use `\q`:

```bash
gigadb=> \q
[vagrant@localhost ~]$

```

It is sometimes useful to make a backup of the PostgreSQL database
that contains GigaDB's dataset metadata. This can be done using the
`pg_dump` tool in the guest VM and using `vagrant` when requested for
the password:

```bash
$ vagrant ssh
$ cd /vagrant/sql
$ pg_dump -U gigadb -h localhost -W -F plain gigadb > backup.sql
```

### Database operation using pgAdmin3

The pgAdmin3 GUI client for PostgreSQL databases can be used to
operate the gigadb database. If you are using a Mac or Windows
computer, download pgAdmin from its [download webpage](http://www.pgadmin.org/download/).
If you are using Linux, pgAdmin can be installed using your package
manager. For example, on Ubuntu:

```bash
$ sudo apt-get install pgadmin3
```

The connection to the gigadb PostgreSQL database on the Vagrant VM
will be made using SSH tunneling which involves tunneling network
traffic using the SSH connection from your host computer to the guest
Vagrant VM.

To create a connection the database, select `Add server` from the
`File` menu or click on the plug icon on the pgAdmin GUI window. A
`New Server Registration` box will appear in which you need to click on
the `SSH Tunnel` tab. The text fields in this box need to completed
as follows:

<img src="https://github.com/gigascience/gigadb-website/blob/develop/images/docs/pgadmin1.png?raw=true">

Next, the connection to the PostgreSQL database itself needs to be
set up:

<img src="https://github.com/gigascience/gigadb-website/blob/develop/images/docs/pgadmin2.png?raw=true">

`gigadb` should appear as an available database with a successful
PostgreSQL connection to the Vagrant VM:

<img src="https://github.com/gigascience/gigadb-website/blob/develop/images/docs/pgadmin3.png?raw=true">
