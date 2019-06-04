# tus-uppy-proto

## deploy
```
$ terraform plan

$ terraform apply

$ ansible-playbook -i inventories/hosts -i /usr/local/bin/terraform-inventory playbook.yml --vault-password-file ~/.vault_pass.txt
```

## access the database

```
$ docker-compose exec database psql -h localhost -U proto -d proto
```

## show ftp account for a user

```
$ docker-compose exec ftpd pure-pw show d-100003 -f /etc/pure-ftpd/passwd/pureftpd.passwd
```