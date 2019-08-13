# GigaDB File Upload Wizard

Web application for authors to upload dataset of accepted papers, for reviewers to audit the uploaded dataset and for curators to publish the dataset to the public.

## Start the prototype

```
./yii prototype/setup --protoUrl http://fuw-proto-dev.pommetab.com:9170/ --apiUrl http://fuw-admin-api/filedrop-accounts --tusUrl http://fuw-proto-dev.pommetab.com:9170/files/
```

## Accessing the docker daemon from container

if using a mac, first install socat using brew:
```
$ brew install socat
```
then do:
```
$ socat TCP-LISTEN:2375,reuseaddr,fork UNIX-CONNECT:/var/run/docker.sock &
```

because Docker for Mac doesn't allow  the daemon on a TCP port. The above steps are not necessary on Windows or Linux.

Using a php dev container (test or console services), one can then use:

```
$ docker-compose exec console bash
# echo -e "GET /info HTTP/1.0\r\n" | nc -v host.docker.internal 2375 | awk 'NR==1,/^\r$/ {next} {printf "%s%s",$0,RT}' | jq
```

For security, do not mount directly the Docker unix socket in any container. TCP socket access is the safe method.

## Workflow

![File Upload Wizard Workflow](img/workflow.png)