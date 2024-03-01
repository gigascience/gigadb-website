#!/usr/bin/env bash

env "PATH=$PATH" chokidar "gigadb/app/client/web/src/**" -c "docker-compose run --rm js sh -c 'npm run build ; npm run deploy'"
