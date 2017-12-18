# Using Docker to run one process per container

```bash
vagrant up
vagrant ssh
cd /vagrant/docker/docker-no-chef
docker-compose up
```
Use browser to check [http://192.168.42.10:8080](http://192.168.42.10:8080)

To test the php container, point your browser to [http://192.168.42.10:8080/index.php](http://192.168.42.10:8080/index.php)