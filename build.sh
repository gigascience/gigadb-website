#!/bin/bash

declare -a app_dirs=('assets' 'protected/runtime');

# Create app_dirs for Yii framework
for dir in "${app_dirs[@]}"
do
    if [[ ! -e $dir ]]; then
        mkdir -p $dir
        chmod a+w $dir
    elif [[ ! -d $dir ]]; then
        echo "$dir already exists but is not a directory" 1>&2
    fi
done

# Call docker build
sudo docker build -t test .
