#!/bin/bash

# Make sure we're not confused by old, incompletely-shutdown httpd
# context after restarting the container.  httpd won't start correctly
# if it thinks it is already running.
#rm -rf /run/httpd/*

exec /usr/bin/chef-solo -l info -c /vagrant/docker/docker-chef-solo/solo.rb -j /vagrant/dokcer/docker-chef-solo/node.json
