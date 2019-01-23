#!/usr/bin/env bash

set -e
set -u
set -x

./bin/phpcpd --log-pmd=/var/www/protected/runtime/phpcpd-gigadb-report.xml protected



