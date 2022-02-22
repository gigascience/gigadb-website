# Gitlab runner

## Set value for REGISTRATION_TOKEN

```
$ cp env-sample .env
```

and fill in the value as instructed.

## Register a runner for a team member

You must use the Gitlab user login (GITLAB_USER_LOGIN).

```
$ docker-compose run --rm -e RUNNER_TAG_LIST="<Gitlab user login here>" register
```

## Start runners

Update ``config/config.toml`` to ensure the value of the ``concurrent``
variables matches the number of ``[[runners]]`` subsections multiplied by the value of the  ``limit`` variable.

then: 

```
$ docker-compose up -d runner
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
* https://docs.gitlab.com/runner/configuration/runner_autoscale_aws/