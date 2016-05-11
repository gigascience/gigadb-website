# GigaDB Development

## Database

It is sometimes useful to make a backup of the PostgreSQL database
that contains dataset metadata. This can be done using the `pg_dump`
tool in the guest VM:

```bash
$ vagrant ssh
$ cd /vagrant/sql
$ pg_dump -U gigadb -h localhost -W -F plain gigadb > backup.sql