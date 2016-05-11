# GigaDB Development

## Database

It is sometimes useful to make a backup of the PostgreSQL database
that contains GigaDB's dataset metadata. This can be done using the
`pg_dump` tool in the guest VM and using `vagrant` when requested for
the password:

```bash
$ vagrant ssh
$ cd /vagrant/sql
$ pg_dump -U gigadb -h localhost -W -F plain gigadb > backup.sql