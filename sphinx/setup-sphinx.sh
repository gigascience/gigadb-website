#!/bin/bash

isInstalled() {
    if hash searchd 2>/dev/null; then
        echo "Sphinx installed"
        return 1
    else
        echo "searchd not found. Please install Sphinx."
        exit 127
    fi
}

isRunning() {
    if ps aux | grep "searchd" > /dev/null
    then
        echo "Sphinx running"
        return 1
    else
        echo "Sphinx stopped"
        return 0
    fi
}

#Check sphinx is installed
isInstalled

#Check sphinx server is running
if isRunning $1; then
    echo "Stopping sphinx server"
    searchd --stop
fi

#Create Sphinx indices
indexer --config /etc/sphinx/sphinx.conf --all

#Restart Sphinx
searchd --config /etc/sphinx/sphinx.conf
