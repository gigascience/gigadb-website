# Troubleshooting performances issues on live production environment

## Architecture of concerns
```
                                                                                                                                    
                                                                                                                                    
                                                                                                                                    
                                                                                                                                    
                                                                                                                                    
                     ┌─────────────────────────────────────────────────────────────┐          ┌───────────────────────────────┐     
                     │                                                             │          │                               │     
                     │                                                             │          │                               │     
                     │  ┌─────────────────┐     ┌────────────────────────────────┐ │          │                               │     
                     │  │ nginx master    │     │php-fpm master                  │ │          │     ┌─────────────────┐       │     
         xx          │  │                 │     │                                │ │          │     │                 │       │     
       xxxxxx────────┼──┤                 ├─────┤  ┌─────────┐   ┌──────────┐    ├─┼──────────┼─────┤ PostgresSQL     │       │     
     xxxxxxxxx       │  │                 │     │  │OpCache  │   │APCu      │    │ │          │     │                 │       │     
    xx xxxxxx        │  │                 │     │  └─────────┘   └──────────┘    │ │          │     └─────────────────┘       │     
     xxx             │  │                 │     │                                │ │          │                               │     
                     │  └──────┬──────────┘     └───────┬────────────────────────┘ │          │                               │     
      Web            │         │                        │                          │          │                               │     
                     │         │                        │                          │          │                               │     
                     │         │                        │                          │          │                               │     
                     │     ┌───┴────────────────┐     ┌─┴────────────────────────┐ │          │                               │     
                     │     │nginx worker process│     │php-fpm pool www          │ │          │                               │     
                     │     │                    │     │                          │ │          │                               │     
                     │     └────────────────────┘     └──────────────────────────┘ │          │                               │     
                     │       module.ec2_dockerhost                                 │          │  module.rds                   │     
                     └─────────────────────────────────────────────────────────────┘          └───────────────────────────────┘     
                                                                                                                                    
                                                                                                                                    
                                                                                                                                    
                                                                                                                                    
                                                                                                                                    
```

## Error in PHP-FPM logs: server reached pm.max_children setting (5), consider raising it 

The performance parameters for PHP-FPM are not configured appropriately.
They are now controlled by the following variables:

| Variable | Example value | Used in | Comments |
|----------|---------------|---------|----------|
| PHP_FPM_MAX_CHILDREN | | Production-Dockerfile | The maximum number of child processes | 
| PHP_FPM_START_SERVERS | | Production-Dockerfile | The number of child processes created on startup |
| PHP_FPM_MIN_SPARE_SERVERS | | Production-Dockerfile | The desired minimum number of idle server processes |
| PHP_FPM_MAX_SPARE_SERVERS | | Production-Dockerfile | The desired maximum number of idle server processes |


To calculate the proper value, you need to consider the total number of RAM on the Web server (module.ec2_dockerhost),
remove the allocation for OpCache (128MB), the allocation for APCu (2048 MB), remove the size of all nginx processes (master and worker),
then divide the remainder by the size of php-fpm pool www processes.

To find the size of process you need to log in to the web sever and run the following for nginx processes:
```
$ ps -C nginx -o rss=
```
Then add up all the line to get total nginx RAM usage.

For php-fpm pool www process: 
```
$ ps -C php-fpm -o rss=
```
Ignore the first line which pertain to the master process. All the other lines are for the child processes and you can average and round to next easy number above for safety:
```
$ ps --no-headers -o "rss,cmd" -C php-fpm | awk '{ sum+=$1 } END { printf ("%d%s\n", sum/NR/1024,"M") }'
```

the result of the calculcation can be use to populate the `PHP_FPM_MAX_CHILDREN` value in Gitlab variables.
The value for the other variables can be derived from the first one. E.g: 
```
pm.start_servers = [25% of max_children]
pm.min_spare_servers = [25% of max_children]
pm.max_spare_servers = [75% of max_children]
```
according to https://chrismoore.ca/2018/10/finding-the-correct-pm-max-children-settings-for-php-fpm/

Or:
```
start_servers	Number of CPU cores x 4
min_spare_servers	Number of CPU cores x 2
max_spare_servers	Same as start_servers
```
according to https://tideways.com/profiler/blog/an-introduction-to-php-fpm-tuning

When changes have been made to the Gitlab variables, to effect the chnage we need to rerun the build job adn the deploy job in the Gitlab pipeline.

## CPU usage of Postgresql is a constant 99%

The following may be happening in combination:

* The website is under heavy load with many requests that make many queries to the database
* The in-memory cache is too small and results from database queries get evicted quickly
  
### Ensuring the cache is appropriately sized

We should set the cache to big enough to hold the entire database content (for the ideal scenario where all datasets are cached). 

Running query on Upstream live: 

```
SELECT schema_name, 
pg_size_pretty(sum(table_size)::bigint),
(sum(table_size) / pg_database_size(current_database())) * 100
FROM (
SELECT pg_catalog.pg_namespace.nspname as schema_name,
pg_relation_size(pg_catalog.pg_class.oid) as table_size
FROM pg_catalog.pg_class
JOIN pg_catalog.pg_namespace ON relnamespace = pg_catalog.pg_namespace.oid
)  t
GROUP BY schema_name
ORDER BY schema_name;
```
Returns:

```
information_schema	88 kB	0.004525927635973054660400
pg_catalog	8360 kB	0.42996312541744019300
pg_toast	15 MB	0.79203733629528456600
public	1873 MB	98.64012413823128647500
```

So, our public schema weights 1873MB, so to be on the safe side, we can set the cache to 2048MB on Upstream deployments.

To verify the current value in in-use, you can login to the web server (module.ec2_dockerhost) and run: 
```
$ docker exec gigadb-website_application_1 php -i | grep apc.shm_size
```

The size of APCu in-memory cache is controlled with Gitlab variable `PHP_APCU_MEMORY` and requires to rerun the build job adn the deploy job in the Gitlab pipeline

It is advised to pre-warm the cache before any deployment that obliterate the in-memory cache. E.g:

on a separate terminal, run:
```
$ wget --directory-prefix=/tmp --spider --recursive --no-directories --quiet https://beta.gigadb.org
```

### Identify the source of the heavy load, it may surface bugs, malicious activity, or under-provisioning

Analyze the logs for Nginx container and PHP-FPM container at portainer.gigadb.org for patterns.

Nginx configuration and fail2ban are two levers for weeding out non-authentic activities spoted in the logs

### PHP-FPM and APCu are configured appropriately for the instance specs but the website is still heavy pressure

Immedidate alleviation is to upgrade the instance specs to tier above the current one.
First update the file `ops/infrastructure/envs/live/terraform.tfvars` and set the variable `web_ec2_type` to the desired instance type.
Then update the environment with terraform:
```
$ cd ops/infrastructure/envs/live
$ terraform plan -target module.ec2_dockerhost
$ terraform apply -target module.ec2_dockerhost
```
Then redeploy the application from Gitlab pipeline (`ld_gigadb`)

>Note1: There will be downtime after the Terraform plan have been applied
>Note2: Currently auhorised instance types are: 
```
"t3.nano",
"t3.micro",
"t3a.small",
"t3.small",
"t3.medium",
"t3.large",
"t3.xlarge",
"t3.2xlarge"
```

When website is working normally again, refers to previous section to investigate and remove root cause of the issue.
When fixed, the same procedure can be used to bring the EC2 instant type to pevious level.


