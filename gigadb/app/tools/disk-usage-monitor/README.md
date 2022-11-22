# Disk Usage Monitoring

to monitor disk usage on our EC2 instances running Centos Stream Linux

## How to

setup a daily cronjob with:

```
HOME=/home/centos
swatch -c swatch.conf -p ./diskUsage
```
