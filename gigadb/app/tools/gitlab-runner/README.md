# Gitlab runner

## Set value for REGISTRATION_TOKEN

```
$ cp env-sample .env
```

and fill in the value as instructed.

## Register a runner

```
$ docker compose run --rm register
```

## Start a runner in standalone Docker Engine

```
$ docker compose up -d runner
```

## Shutdown a runner in standalone Docker engine

Fist, de-register it in Gitlab dashboard, then

```
$ docker compose down -v
```


## Resources 

* https://docs.gitlab.com/runner/configuration/advanced-configuration.html
* https://docs.gitlab.com/ee/ci/runners/configure_runners.html
* https://docs.gitlab.com/runner/register/