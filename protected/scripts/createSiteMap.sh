#!/bin/bash
BASEDIR=$(dirname $0)
${BASEDIR}/../yiic createsitemap
${BASEDIR}/../yiic autosearch search
${BASEDIR}/../yiic autosendnotification notification
