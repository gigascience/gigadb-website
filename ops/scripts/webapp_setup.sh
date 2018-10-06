#!/usr/bin/env bash

set -e -u -x

composer install

./protected/yiic lesscompiler

