#
# Cookbook Name:: workers
# Recipe:: default
#
# install tools to manage background workers for Gigadb website
#
# Copyright 2016, GigaScience
#
# All rights reserved - Do Not Redistribute
#

package 'redis' do
    action :install
end

service 'redis' do
  start_command '/etc/init.d/redis start'
  stop_command '/etc/init.d/redis stop'
  status_command '/etc/init.d/redis status'
  supports [:start, :stop, :status]
  # starts the service if it's not running and enables it to start at system boot time
  action [:enable, :start]
end

package 'beanstalkd' do
    action :install
end

service 'beanstalkd' do
  start_command '/etc/init.d/beanstalkd start'
  stop_command '/etc/init.d/beanstalkd stop'
  status_command '/etc/init.d/beanstalkd status'
  supports [:start, :stop, :status]
  # starts the service if it's not running and enables it to start at system boot time
  action [:enable, :start]
end
