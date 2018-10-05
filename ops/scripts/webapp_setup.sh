#!/usr/bin/env bash

set -e -u -x

composer update

./protected/yiic lesscompiler

