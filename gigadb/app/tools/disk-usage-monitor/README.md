# Disk Usage Monitoring

to monitor disk usage on our EC2 instances running Centos Stream Linux

## Required variables

* GITTER_IT_NOTIFICATION_ROOM_ID=\<From Gitlab variables\>
* GITTER_API_TOKEN=\<From Gitlab variables\>
* GIGADB_ENVIRONMENT=\<From Gitlab variables\>
* DEPLOYMENT_TIER=bastion

## How to


### Disk usage

```
df -h | grep /dev/ | cut -d " " -f10 | cut -d% -f1
```

### Notify

```
source /home/centos/.env;curl -X POST -i -H \"Content-Type: application/json\" -H \"Accept: application/json\" -H \"Authorization: Bearer $GITTER_API_TOKEN\" \"https://api.gitter.im/v1/rooms/$GITTER_IT_NOTIFICATION_ROOM_ID/chatMessages\"  -d '{\"text\":\"Disk space usage requires attention on '\"$DEPLOYMENT_TIER-$GIGADB_ENVIRONMENT\"': '\"$1\"'%\"}'
```

### Deployment

Run the bastion playbook

### Testing

```
swatch -c check-swatch.conf -p ./diskUsage
```