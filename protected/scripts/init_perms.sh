#!/bin/bash

# Set up permissions

if [ -f /etc/redhat-release ] ; then
    # RedHat/CentOS
    GROUP=apache
else
    # Debian/Ubuntu
    GROUP=www-data
fi

OWNER=gigadb

set -e

CURDIR=$PWD
BINDIR=`dirname $0`
cd $BINDIR; BINDIR=$PWD; cd $CURDIR

BASEDIR="$BINDIR/../.."

cd $BASEDIR; BASEDIR=$PWD; cd $CURDIR

echo "BASEDIR=$BASEDIR"

if [ ! -d $BASEDIR/protected ] ; then
    echo "BASEDIR incorrect, should be the base htdocs directory"
    exit 1;
fi

# Yii runtime directories
for component in $(echo assets protected/runtime images/uploads)
do
    the_dir=$BASEDIR/$component
    mkdir -p $the_dir
    chown -R $OWNER:$GROUP $the_dir
    chmod -R g+rw $the_dir
done
