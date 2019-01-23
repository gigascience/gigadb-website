#!/usr/bin/env bash

set -e
set -u
set -x

cp ops/configuration/phpcs-conf/phpcs.phpdoc.xml /var/www/phpcs.xml
./bin/phpcs --report=source \
			--report-file=protected/runtime/phpcs-phpdoc-source.txt \
			--report-summary=protected/runtime/phpcs-phpdoc-summary.txt \
			--report-full=protected/runtime/phpcs-phpdoc-full.txt

