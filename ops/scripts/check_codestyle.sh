#!/usr/bin/env bash

set -e
set -u
set -x

cp ops/configuration/phpcs-conf/phpcs.psr2.xml /var/www/phpcs.xml
./bin/phpcs --report=source \
			--report-file=protected/runtime/phpcs-psr2-source.txt \
			--report-summary=protected/runtime/phpcs-psr2-summary.txt \
			--report-full=protected/runtime/phpcs-psr2-full.txt

